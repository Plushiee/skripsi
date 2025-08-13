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
    protected $description = 'Mengirimkan status pompa via MQTT';

    protected $lastPublishedStatus = null;

    public function handle()
    {
        $mqtt = null;
        $isPompaChanged = false;
        $isSuhuChanged = false;
        $lastPompaId = null;
        $lastSuhuId = null;

        while (true) {
            try {
                if (!$mqtt || !$mqtt->isConnected()) {
                    $mqtt = $this->connectToMqtt();
                    if (!$mqtt || !$mqtt->isConnected()) {
                        Log::warning("MQTT disconnected. Reconnecting...");
                        continue;
                    }
                }

                $this->publishDumpData($mqtt);

                $pompa = TabelPompaModel::orderByDesc('id')->first();
                if ($pompa && $pompa->id === $lastPompaId) {
                    $isPompaChanged = false;
                } else {
                    $lastPompaId = $pompa ? $pompa->id : null;
                    $isPompaChanged = true;
                }

                // echo sprintf("Current pump ID: %s\n", $pompa ? $pompa->id : 'None');
                // echo sprintf("otomatis: %s, status: %s\n", $pompa ? $pompa->otomatis : 'None', $pompa ? $pompa->status : 'None');

                $cachedData = cache('sse-update-event', []);
                if (!empty($cachedData) && isset($cachedData['tempHum']['temperature'])) {
                    $suhu = (object)[
                        'temperature' => $cachedData['tempHum']['temperature'],
                        'id' => $cachedData['tempHum']['id'] ?? null
                    ];
                } else {
                    $suhu = TabelTempHumModel::orderByDesc('id')->first();
                }

                $isSuhuChanged = false;
                if ($suhu && isset($suhu->id) && $suhu->id === $lastSuhuId) {
                    $isSuhuChanged = false;
                } else {
                    $lastSuhuId = $suhu && isset($suhu->id) ? $suhu->id : null;
                    $isSuhuChanged = true;
                }

                // echo sprintf("Current temperature ID: %s, Temperature: %s\n", $suhu ? $suhu->id : 'None', $suhu ? $suhu->temperature : 'None');

                if ($pompa && $pompa->otomatis == 1) {
                    // echo sprintf("Otomatis mode: Checking temperature for pump ID %d\n", $pompa->id);
                    if ($suhu && $suhu->temperature > $pompa->suhu) {
                        if ($isPompaChanged || $isSuhuChanged) {
                            $this->publishPumpStatus($mqtt, 'nyala');
                        }
                    } else {
                        if ($isPompaChanged || $isSuhuChanged) {
                            $this->publishPumpStatus($mqtt, 'mati');
                        }
                    }
                } elseif ($pompa) {
                    $this->publishPumpStatus($mqtt, $pompa->status);
                }

                sleep(1);
            } catch (MqttClientException $e) {
                Log::error("MQTT Client error: " . $e->getMessage());
                $mqtt = null;
                sleep(1);
                continue;
            } catch (\Throwable $e) {
                Log::error("Unexpected error: " . $e->getMessage());
                $mqtt = null;
                sleep(1);
                continue;
            }
        }
    }


    protected function connectToMqtt()
    {
        try {
            $mqtt = MQTT::connection('default');

            if (!$mqtt->isConnected()) {
                $mqtt->connect(null, true, ['keep_alive' => 10]);
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
            $mqtt->publish('72210456/dump_publish', 'dump', 0);
        } catch (MqttClientException $e) {
            Log::error("Failed to publish message: " . $e->getMessage());
            throw $e;
        }
    }
}
