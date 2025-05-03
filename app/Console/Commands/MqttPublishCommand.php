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

    protected $lastPublishedStatus = null;

    public function handle()
    {
        while (true) {
            try {
                $mqtt = $this->connectToMqtt();
                $this->publishDumpData($mqtt);

                $pompa = TabelPompaModel::orderByDesc('id')->first();
                $suhu = TabelTempHumModel::orderByDesc('id')->first();

                if ($pompa) {
                    if ($pompa->otomatis == 1) {
                        if ($suhu) {
                            if ($pompa->suhu < $suhu->temperature) {
                                $this->publishPumpStatus($mqtt, 'nyala');
                            } else {
                                $this->publishPumpStatus($mqtt, 'mati');
                            }
                        } else {
                            $this->publishPumpStatus($mqtt, 'mati');
                        }
                    } else {
                        $this->publishPumpStatus($mqtt, $pompa->status);
                    }
                }

                sleep(3);
            } catch (MqttClientException $e) {
                Log::error("MQTT error: " . $e->getMessage());
                sleep(4);
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
        if ($this->lastPublishedStatus !== $status) {
            try {
                $mqtt->publish('72210456/pump', $status, 0);
                $this->lastPublishedStatus = $status;
            } catch (MqttClientException $e) {
                Log::error("Failed to publish message: " . $e->getMessage());
                throw $e;
            }
        }
    }

    protected function publishDumpData($mqtt)
    {
        try {
            $mqtt->publish('72210456/dump', 'dump', 0);
        } catch (MqttClientException $e) {
            Log::error("Failed to publish message: " . $e->getMessage());
            throw $e;
        }
    }
}
