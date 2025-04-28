<?php

namespace App\Console\Commands;

use App\Models\TabelPompaModel;
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
    protected $pingData = [];


    public function __construct()
    {
        parent::__construct();
    }

    // Implementasi logika untuk berlangganan ke topik MQTT
    public function handle()
    {
        $lastMessageTime = now(); // Tambahkan: waktu terakhir menerima message

        while (true) {
            try {
                $mqtt = MQTT::connection('default');

                $topics = [
                    '72210456/waterflow',
                    '72210456/totalmilliLiters',
                    '72210456/humidityDHT',
                    '72210456/temperatureDHT',
                    '72210456/TDS',
                    '72210456/ping',
                    '72210456/esp8266_sensor',
                    '72210456/esp8266_relay',
                    '72210456/PH',
                    '72210456/temp_luar',
                    '72210456/temp_dalam',
                    '72210456/pump',
                    '72210456/pump_relay',
                    '72210456/dump'
                ];

                foreach ($topics as $topic) {
                    $mqtt->subscribe($topic, function (string $topic, string $message) use (&$lastMessageTime) {
                        $lastMessageTime = now(); // Update setiap ada pesan baru
                        $this->handleMessage($topic, $message);
                    }, 0);
                    sprintf("Subscribed to topic: %s\n", $topic);
                }

                while (true) {
                    $mqtt->loop(true);

                    // Tambahan: cek apakah sudah lewat 5 detik tanpa message
                    if (now()->diffInSeconds($lastMessageTime) >= 5) {
                        $this->pushDefaultCache();
                        $lastMessageTime = now();  // Reset timer setelah push
                    }

                    usleep(100000); // Delay kecil (0.1 detik) supaya tidak berat CPU
                }
            } catch (MqttClientException $e) {
                $this->error("MQTT error: " . $e->getMessage());
                sleep(5);
                continue;
            }
        }

        return 0;
    }


    // Fungsi Handle message yang masuk
    protected function handleMessage($topic, $message)
    {
        // Update data yang diterima berdasarkan topik
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

        // Kirim ke cache jika data telah diterima
        cache()->put('sse-update-event', $this->koleksiData, now()->addSeconds(5));


        // Simpan ke database sesuai topik
        $this->saveToDatabase($topic, $message);
    }

    // Fungsi menyimpan data ke database
    protected function saveToDatabase($topic, $message)
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
                $isDifferent = !$lastRecord || $lastRecord->status != $message;
                if ($isDifferent) {
                    TabelPompaModel::create(['id_area' => 1, 'status' => $message, 'otomatis' => $lastRecord->otomatis ?? 0, 'suhu' => $lastRecord->suhu ?? null]);
                }
                break;
            case '72210456/dump':
                break;
        }
    }

    // fungsi menyimpan data suhu dan kelembaban dalam satu Tabel
    protected function storeTempHumData()
    {
        // Menyimpan data suhu dan kelembapan ke dalam array
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

    // Fungsi cek apakah data berbeda
    protected function isBedaData($model, $column, $newValue)
    {
        $lastRecord = app($model)::latest('created_at')->first();
        return !$lastRecord || $lastRecord->$column != $newValue;
    }

    // Fungsi untuk mengisi default cache
    protected function pushDefaultCache()
    {
        $defaultData = [
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

        cache()->put('sse-update-event', $defaultData, now()->addSeconds(5));
    }
}
