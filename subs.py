import random
import datetime
import time
from paho.mqtt import client as mqtt_client

broker = 'broker.emqx.io'
port = 1883
topic1 = '72210456/waterflow'
topic2 = '72210456/totalmilliLiters'
topic3 = '72210456/humidityDHT'
topic4 = '72210456/temp_luar'
topic5 = '72210456/TDS'
topic6 = '72210456/ping'
topic7 = '72210456/esp8266_sensor'
topic8 = '72210456/error_1'
topic9 = '72210456/esp8266_2'
topic10 = '72210456/PH'
client_id = f'python-mqtt-{random.randint(0, 1000)}'
username = 'qwerty'
password = 'public'
output_file = 'D:\\Kuliah\\semester 6\\INTERNET OF THINGS\\Tugas 3\\72210456_MQTT.txt'

def connect_mqtt() -> mqtt_client:
    def on_connect(client, userdata, flags, rc):
        if rc == 0:
            print("Connected to MQTT Broker!")
            for topic in [topic1, topic2, topic3, topic4, topic5, topic6, topic7, topic8, topic9, topic10]:
                client.subscribe(topic)
        else:
            print("Failed to connect, return code %d\n", rc)

    client = mqtt_client.Client(client_id)
    client.username_pw_set(username, password)
    client.on_connect = on_connect
    client.connect(broker, port)
    return client

def publish(client):
    # Nilai awal untuk setiap topik
    current_values = {
        topic1: random.uniform(650.0, 900.0),
        topic2: random.randint(600, 1000),
        topic3: random.uniform(50.0, 80.0),
        topic4: random.uniform(23.0, 27.0),
        topic5: random.uniform(900.0, 1200.0),
        topic6: random.uniform(80.0, 100.0),
        topic7: "1",
        topic10: random.uniform(8.0, 12.0),
    }

    while True:
        for topic, value in current_values.items():
            if topic == topic7:
                msg = "1"  # Langsung tetapkan msg sebagai "1"
            else:
                max_change_percentage = 0.02  # 2%
                change = value * random.uniform(-max_change_percentage, max_change_percentage)
                new_value = value + change
                current_values[topic] = new_value

                # Format nilai
                if topic in [topic1, topic3, topic4, topic5, topic6, topic10]:
                    msg = f"{new_value:.2f}"
                else:
                    msg = f"{int(new_value)}"

            # Kirim nilai ke broker MQTT
            result = client.publish(topic, msg)
            status = result[0]

            if status != 0:
                # print(f"Send `{msg}` to topic `{topic}`")
                print(f"Failed to send message to topic {topic}")
            # else:
            #     print(f"Failed to send message to topic {topic}")

        time.sleep(2)  # Mengirim data setiap 2 detik

def on_message(client, userdata, msg):
    message = f"{msg.topic}: `{msg.payload.decode()}` at {datetime.datetime.now()}\n"
    # print(message)
    # write_to_file(message)

def write_to_file(message):
    with open(output_file, 'a') as file:
        file.write(message)

def run():
    client = connect_mqtt()
    # client.on_message = on_message
    client.loop_start()
    publish(client)

if __name__ == '__main__':
    run()
