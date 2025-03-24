<?php

namespace App\Console\Commands;

use App\Models\TabelTempHumModel;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\TabelPompaModel;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Illuminate\Support\Facades\Log;

class MqttPublishCommand extends Command
{
    protected $signature = 'mqtt:publish';
    protected $description = 'Mengirimkan status pompa via MQTT setiap 5 detik';

    protected $counter = 0;

    public function handle()
    {
        while (true) {
            try {
                $mqtt = $this->connectToMqtt();
                $this->counter++;
                $this->publishDumpData($mqtt);

                if ($this->counter > 5) {
                    $pompa = TabelPompaModel::latest()->first();
                    $suhu = TabelTempHumModel::latest()->first();
                    if ($pompa) {
                        if ($pompa->otomatis == 1) {
                            if ($pompa->suhu > $suhu->suhu) {
                                $this->publishPumpStatus($mqtt, 'nyala');
                            } else {
                                $this->publishPumpStatus($mqtt, 'mati');
                            }
                        } else {
                            $this->publishPumpStatus($mqtt, $pompa->status);
                        }
                    }

                    $this->counter = 0;
                }

                sleep(3);
            } catch (MqttClientException $e) {
                Log::error("MQTT error: " . $e->getMessage());
                sleep(4); // Tunggu sebentar sebelum mencoba lagi
            } catch (\Exception $e) {
                Log::error("General error: " . $e->getMessage());
                sleep(4); // Tunggu sebentar sebelum mencoba lagi
            }
        }
    }

    protected function connectToMqtt()
    {
        $mqtt = MQTT::connection('default');
        if (!$mqtt->isConnected()) {
            $mqtt->connect(null, true, ['keep_alive' => 60]);
        }
        return $mqtt;
    }

    protected function publishPumpStatus($mqtt, $status)
    {
        try {
            // echo sprintf("Publishing Pump Status: %s\n", $status);
            $mqtt->publish('72210456/pump', $status, 0);
        } catch (MqttClientException $e) {
            Log::error("Failed to publish message: " . $e->getMessage());
            throw $e;
        }
    }

    protected function publishDumpData($mqtt)
    {
        try {
            // echo sprintf("Publishing Pump Status: %s\n", $status);
            $mqtt->publish('72210456/dump', 'dump', 0);
        } catch (MqttClientException $e) {
            Log::error("Failed to publish message: " . $e->getMessage());
            throw $e;
        }
    }
}
