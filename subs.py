import random
import datetime
import time
import threading
from paho.mqtt import client as mqtt_client

broker = 'mosquitto.plushi.ee'
port = 1883
topic1 = '72210456/waterflow'
topic2 = '72210456/totalmilliLiters'
topic3 = '72210456/humidityDHT'
topic4 = '72210456/temperatureDHT'
topic5 = '72210456/TDS'
topic6 = '72210456/ping'
topic7 = '72210456/temp_luar'
topic8 = '72210456/temp_dalam'
topic9 = '72210456/esp8266_relay'
topic10 = '72210456/esp8266_sensor'

client_id = f'python-mqtt-{random.randint(0, 1000)}'
username = 'skripsi'
password = 'x70FL]a11Kl2fvOA'

output_file = 'D:\\Kuliah\\semester 6\\INTERNET OF THINGS\\Tugas 3\\72210456_MQTT.txt'

def connect_mqtt() -> mqtt_client:
    def on_connect(client, userdata, flags, rc):
        if rc == 0:
            print("Connected to MQTT Broker!")
            for topic in [topic1, topic2, topic3, topic4, topic5, topic6, topic7, topic8]:
                client.subscribe(topic)
        else:
            print(f"Failed to connect, return code {rc}")

    client = mqtt_client.Client(client_id=client_id, protocol=mqtt_client.MQTTv311)
    client.username_pw_set(username, password)
    client.on_connect = on_connect
    client.connect(broker, port)
    return client


def publish(client):
    current_values = {
        topic1: random.uniform(850.0, 1000.0),
        topic2: random.randint(600, 1000),
        topic3: random.uniform(50.0, 80.0),
        topic4: random.uniform(25.0, 35.0),
        topic5: random.uniform(900.0, 1200.0),
        topic6: random.uniform(80.0, 100.0),
        topic7: random.uniform(25.0, 35.0),
        topic8: random.uniform(25.0, 35.0),
        topic9: "true",
        topic10: "true"
    }

    def publish_loop():
        while True:
            for topic, value in current_values.items():
                if topic not in [topic9, topic10]:
                    max_change_percentage = 0.02
                    change = value * random.uniform(-max_change_percentage, max_change_percentage)
                    value += change
                    current_values[topic] = value
                    msg = f"{value:.2f}" if isinstance(value, float) else f"{int(value)}"
                else:
                    msg = "true"

                result = client.publish(topic, msg)
                if result[0] != 0:
                    print(f"Failed to send message to topic {topic}")
                
                # if result[0] == 0:
                #     print(f"Send `{msg}` to topic `{topic}` at {datetime.datetime.now()}")
                # else:
                #     print(f"Failed to send message to topic {topic}")
            time.sleep(2)

    threading.Thread(target=publish_loop, daemon=True).start()


def on_message(client, userdata, msg):
    message = f"{msg.topic}: `{msg.payload.decode()}` at {datetime.datetime.now()}\n"
    # print(message)
    # write_to_file(message)


def write_to_file(message):
    with open(output_file, 'a') as file:
        file.write(message)


def run():
    client = connect_mqtt()
    client.on_message = on_message
    client.loop_start()
    publish(client)

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Program dihentikan oleh user.")
        client.loop_stop()
        client.disconnect()


if __name__ == '__main__':
    run()
