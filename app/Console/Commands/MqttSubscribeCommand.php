<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Events\MqttSubscribeEvent;
use App\Models\TabelArusAirModel;
use App\Models\TabelPHModel;
use App\Models\TabelPingModel;
use App\Models\TabelTDSModel;
use App\Models\TabelTempHumModel;
use PhpMqtt\Client\Exceptions\MqttClientException;
use App\Events\SSEUpdateEvent;
use App\Models\TabelPompaModel;
use Illuminate\Support\Facades\Log;

class MqttSubscribeCommand extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe ke topik MQTT and menghandle pesan yang masuk';
    protected $tempHumData = [
        'temperature' => null,
        'humidity' => null,
    ];

    protected $koleksiData = [
        'arusAir' => null,
        'tds' => null,
        'tempHum' => [
            'temperature' => null,
            'humidity' => null,
        ],
        'ping' => null,
        'status_sensor' => null,
        'status_relay' => null,
    ];

    protected $temperatureData = [];
    protected $humidityData = [];
    protected $waterFlowData = [];
    protected $tdsData = [];
    protected $phData = [];
    protected $pingData = [];


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $mqtt = null;

        $lastReceivedTime = time();

        while (true) {
            try {
                if (!$mqtt || !$mqtt->isConnected()) {
                    $mqtt = $this->connectToMqtt();
                    if (!$mqtt || !$mqtt->isConnected()) {
                        Log::warning("MQTT disconnected. Reconnecting...");
                        continue;
                    }
                }

                // Topic yang di subscribe
                $topics = [
                    '72210456/waterflow',
                    '72210456/totalmilliLiters',
                    '72210456/humidityDHT',
                    '72210456/temperatureDHT',
                    '72210456/TDS',
                    '72210456/ping',
                    '72210456/esp8266_sensor',
                    '72210456/esp8266_relay',
                    '72210456/temp_luar',
                    '72210456/temp_dalam',
                    '72210456/pump',
                    '72210456/pump_relay',
                    '72210456/dump'
                ];

                foreach ($topics as $topic) {
                    $mqtt->subscribe($topic, function (string $topic, string $message) use ($mqtt) {
                        // echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
                        $this->handleMessage($topic, $message, $mqtt);
                        $lastReceivedTime = time();
                    }, 0);
                }


                if (time() - $lastReceivedTime > 60) { // lebih dari 3 menit
                    Log::warning("Tidak ada data selama 5 menit, exit...");
                    exit(1);
                }

                $mqtt->loop();

                usleep(100000);
            } catch (MqttClientException $e) {
                Log::error("MQTT Client error: " . $e->getMessage());
                $mqtt = null;
                sleep(4);
                continue;
            } catch (\Throwable $e) {
                Log::error("Unexpected error: " . $e->getMessage());
                $mqtt = null;
                sleep(4);
                continue;
            }
        }
    }

    // Fungsi Handle message yang masuk
    protected function handleMessage($topic, $message, $mqtt)
    {
        match ($topic) {
            '72210456/waterflow' => $this->koleksiData['arusAir'] = $message,
            '72210456/TDS' => $this->koleksiData['tds'] = $message,
            '72210456/humidityDHT' => $this->koleksiData['tempHum']['humidity'] = $message,
            '72210456/temp_luar' => $this->koleksiData['tempHum']['temperature'] = $message,
            '72210456/ping' => $this->koleksiData['ping'] = $message,
            '72210456/esp8266_sensor' => $this->koleksiData['status_sensor'] = $message,
            '72210456/esp8266_relay' => $this->koleksiData['status_relay'] = $message,
            default => null,
        };

        if ($topic == '72210456/dump') {
            $mqtt->publish('72210456/dump_subs', 'dump', 0);
        }

        if ($this->isAllDataCollected()) {
            // echo "Data terkumpul: " . json_encode($this->koleksiData) . "\n";
            cache()->put('sse-update-event', $this->koleksiData, now()->addMinutes(3));
            $this->resetkoleksiData();
        }


        // Simpan ke database sesuai topik
        $this->saveToDatabase($topic, $message, $mqtt);
    }

    protected function connectToMqtt()
    {
        try {
            $mqtt = MQTT::connection('default');

            if (!$mqtt->isConnected()) {
                $mqtt->connect(null, true, ['keep_alive' => 60]);
            }

            if (!$mqtt->isConnected()) {
                Log::warning("MQTT failed to connect after attempting.");
            }

            return $mqtt;
        } catch (\Throwable $e) {
            Log::error("MQTT connection error: " . $e->getMessage());
            return null;
        }
    }


    // Fungsi untuk memeriksa apakah semua data telah terkumpul
    protected function isAllDataCollected()
    {
        return isset(
            $this->koleksiData['arusAir'],
            $this->koleksiData['tds'],
            $this->koleksiData['tempHum']['temperature'],
            $this->koleksiData['tempHum']['humidity'],
            $this->koleksiData['ping'],
            $this->koleksiData['status_sensor'],
            $this->koleksiData['status_relay']
        );
    }

    protected function resetkoleksiData()
    {
        $this->koleksiData = [
            'arusAir' => null,
            'tds' => null,
            'tempHum' => [
                'temperature' => null,
                'humidity' => null,
            ],
            'ping' => null,
            'status_sensor' => null,
            'status_relay' => null,
        ];
    }

    protected function saveToDatabase($topic, $message, $mqtt = null)
    {
        switch ($topic) {
            case '72210456/waterflow':
                if ($message != null) {
                    $this->waterFlowData[] = $message;
                }

                if (count($this->waterFlowData) >= 300) {
                    $averageWaterFlow = round(array_sum($this->waterFlowData) / count($this->waterFlowData), 2);

                    $lastRecord = TabelArusAirModel::latest('created_at')->first();
                    $isDifferent = !$lastRecord || $lastRecord->debit != $averageWaterFlow;

                    if ($isDifferent) {
                        TabelArusAirModel::create(['id_area' => 1, 'debit' => $averageWaterFlow]);
                        $this->waterFlowData = [];
                    } else {
                        $this->waterFlowData = [];
                    }
                }
                break;

            case '72210456/TDS':
                if ($message != null) {
                    $this->tdsData[] = $message;
                }

                if (count($this->tdsData) >= 300) {
                    $averageTDS = round(array_sum($this->tdsData) / count($this->tdsData), 2);

                    $lastRecord = TabelTDSModel::latest('created_at')->first();
                    $isDifferent = !$lastRecord || $lastRecord->tds != $averageTDS;

                    if ($isDifferent) {
                        TabelTDSModel::create(['id_area' => 1, 'tds' => $averageTDS]);
                        $this->tdsData = [];
                    } else {
                        $this->tdsData = [];
                    }
                }
                break;
            case '72210456/ping':
                if ($message != null) {
                    $this->pingData[] = $message;
                }

                if (count($this->pingData) >= 300) {
                    $averagePing = round(array_sum($this->pingData) / count($this->pingData), 2);

                    $lastRecord = TabelPingModel::latest('created_at')->first();
                    $isDifferent = !$lastRecord || $lastRecord->ping != $averagePing;

                    if ($isDifferent) {
                        TabelPingModel::create(['id_area' => 1, 'ping' => $averagePing]);
                        $this->pingData = [];
                    } else {
                        $this->pingData = [];
                    }
                }
                break;
            case '72210456/humidityDHT':
                $this->tempHumData['humidity'] = $message;
                $this->storeTempHumData();
                break;
            case '72210456/temp_luar':
                $this->tempHumData['temperature'] = $message;
                $this->storeTempHumData();
                break;
            case '72210456/esp8266_sensor':
                $this->koleksiData['status_sensor'] = $message;
                break;
            case '72210456/esp8266_relay':
                $this->koleksiData['status_relay'] = $message;
                break;
            case '72210456/pump_relay':
                $lastRecord = TabelPompaModel::latest('created_at')->first();
                $isDifferent = $isDifferent = !$lastRecord || $lastRecord->status != $message;

                if ($isDifferent || !$lastRecord) {
                    TabelPompaModel::create(['id_area' => 1, 'status' => $message, 'otomatis' => 0, 'suhu' => $lastRecord->suhu ?? 0]);
                }
                $mqtt->publish('72210456/pump', $message, 0);
                break;
        }
    }

    protected function storeTempHumData()
    {
        if ($this->tempHumData['temperature'] !== null) {
            $this->temperatureData[] = $this->tempHumData['temperature'];
        }
        if ($this->tempHumData['humidity'] !== null) {
            $this->humidityData[] = $this->tempHumData['humidity'];
        }

        if (count($this->temperatureData) >= 300 && count($this->humidityData) >= 300) {
            $averageTemperature = round(array_sum($this->temperatureData) / count($this->temperatureData), 2);
            $averageHumidity = round(array_sum($this->humidityData) / count($this->humidityData), 2);

            $lastRecord = TabelTempHumModel::latest('created_at')->first();
            $isDifferent = !$lastRecord ||
                $lastRecord->temperature != $averageTemperature ||
                $lastRecord->humidity != $averageHumidity;

            if ($isDifferent) {
                TabelTempHumModel::create([
                    'id_area' => 1,
                    'temperature' => $averageTemperature,
                    'humidity' => $averageHumidity
                ]);

                $this->temperatureData = [];
                $this->humidityData = [];
            } else {
                $this->temperatureData = [];
                $this->humidityData = [];
            }
        }
    }
}
