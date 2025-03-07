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
        'ph' => null,
        'tempHum' => [
            'temperature' => null,
            'humidity' => null,
        ],
        'ping' => null,
        'status' => null,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // Implementasi logika untuk berlangganan ke topik MQTT
    public function handle()
    {
        while (true) {
            try {
                $mqtt = MQTT::connection('default');

                // $mqtt = MQTT::connection([
                //     'max_inflight_messages' => 100, // Tingkatkan batas pesan yang sedang diproses
                // ]);

                // Array of topics to subscribe
                $topics = [
                    '72210456/waterflow',
                    '72210456/totalmilliLiters',
                    '72210456/humidityDHT',
                    '72210456/temperatureDHT',
                    '72210456/TDS',
                    '72210456/ping',
                    '72210456/esp8266_sensor',
                    '72210456/PH',
                    '72210456/temp_luar',
                    '72210456/temp_dalam',
                ];

                // Subscribe to each topic
                foreach ($topics as $topic) {
                    $mqtt->subscribe($topic, function (string $topic, string $message) {
                        // echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
                        $this->handleMessage($topic, $message);
                    }, 0);
                }

                // Start the loop to listen for incoming messages
                $mqtt->loop(true);
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
            '72210456/PH' => $this->koleksiData['ph'] = $message,
            '72210456/humidityDHT' => $this->koleksiData['tempHum']['humidity'] = $message,
            '72210456/temp_luar' => $this->koleksiData['tempHum']['temperature'] = $message,
            '72210456/ping' => $this->koleksiData['ping'] = $message,
            '72210456/esp8266_sensor' => $this->koleksiData['status'] = $message,
            default => null,
        };

        // Kirim ke cache jika semua data telah diterima
        if ($this->isAllDataCollected()) {
            // echo sprintf('Data lengkap, menyimpan ke cache: ', $this->koleksiData);
            cache()->put('sse-update-event', $this->koleksiData, now()->addSeconds(10));
            $this->resetkoleksiData();
        }


        // Simpan ke database sesuai topik
        $this->saveToDatabase($topic, $message);
    }

    // Fungsi untuk memeriksa apakah semua data telah terkumpul
    protected function isAllDataCollected()
    {
        return isset(
            $this->koleksiData['arusAir'],
            $this->koleksiData['tds'],
            $this->koleksiData['ph'],
            $this->koleksiData['tempHum']['temperature'],
            $this->koleksiData['tempHum']['humidity'],
            $this->koleksiData['ping'],
            $this->koleksiData['status']
        );
    }

    // Fungsi untuk mereset data yang dikumpulkan
    protected function resetkoleksiData()
    {
        $this->koleksiData = [
            'arusAir' => null,
            'tds' => null,
            'ph' => null,
            'tempHum' => [
                'temperature' => null,
                'humidity' => null,
            ],
            'ping' => null,
            'status' => null,
        ];
    }

    // Fungsi menyimpan data ke database
    protected function saveToDatabase($topic, $message)
    {
        switch ($topic) {
            case '72210456/waterflow':
                TabelArusAirModel::create(['id_area' => 1, 'debit' => $message]);
                break;
            case '72210456/TDS':
                TabelTDSModel::create(['id_area' => 1, 'ppm' => $message]);
                break;
            case '72210456/PH':
                TabelPHModel::create(['id_area' => 1, 'ph' => $message]);
                break;
            case '72210456/humidityDHT':
                $this->tempHumData['humidity'] = $message;
                $this->storeTempHumData();
                break;
            case '72210456/temp_luar':
                $this->tempHumData['temperature'] = $message;
                $this->storeTempHumData();
                break;
            case '72210456/ping':
                TabelPingModel::create(['id_area' => 1, 'ping' => $message]);
                break;
            case '72210456/esp8266_sensor':
                $this->koleksiData['status'] = $message;
                // Log::info('Status: ' . $message);
                break;
        }
    }

    // fungsi menyimpan data suhu dan kelembaban dalam satu Tabel
    protected function storeTempHumData()
    {
        $lastRecord = TabelTempHumModel::latest('created_at')->first();
        if ($this->tempHumData['temperature'] !== null && $this->tempHumData['humidity'] !== null) {
            $isDifferent = !$lastRecord ||
                $lastRecord->temperature != $this->tempHumData['temperature'] ||
                $lastRecord->humidity != $this->tempHumData['humidity'];

            if ($isDifferent) {
                TabelTempHumModel::create([
                    'id_area' => 1,
                    'temperature' => $this->tempHumData['temperature'],
                    'humidity' => $this->tempHumData['humidity']
                ]);

                // Reset data after saving
                $this->tempHumData['temperature'] = null;
                $this->tempHumData['humidity'] = null;
            }
        }
    }

    // Fungsi cek apakah data berbeda
    protected function isBedaData($model, $column, $newValue)
    {
        $lastRecord = app($model)::latest('created_at')->first();
        return !$lastRecord || $lastRecord->$column != $newValue;
    }
}
