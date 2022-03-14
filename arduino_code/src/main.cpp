#include <Arduino.h>


#include <SPI.h>
#include <WiFiNINA.h>


#include "secrets.h"


//Tout ce qui concerne le wifi
char ssid[] = SECRET_SSID;        // your network SSID (name)
char pass[] = SECRET_PASS;      // your network password (use for WPA, or use as key for WEP)
int status = WL_IDLE_STATUS;     // le status du wifi

//Variable client de la classe WiFiClient
WiFiClient client;

void setup()
{
    Serial.begin(9600);
    pinMode(2, OUTPUT);

    // Ici on va essayer de se connecter au wifi
    while ( status != WL_CONNECTED) {
        status = WiFi.begin(ssid, pass);
        Serial.println("Trying connection to wifi...");
        // wait 5 seconds for connection to the wifi
        delay(5000);
    }

    IPAddress ardIpAddress = WiFi.localIP();
    Serial.println(ardIpAddress);

    char server[] = "172.16.0.7";

    if (client.connect(server, 80)) {

        Serial.println("connected to server");

        // Make a HTTP request:

        client.println("GET / HTTP/1.1");

        client.println("Host: arduino-web");

        client.println("Connection: close");

        client.println();

    }

}

void ledFadeInAndOut()
{
    for(int i=0; i<255; i++){
    analogWrite(2, i);
    delay(5);
    }
    for(int i=255; i>0; i--){
        analogWrite(2, i);
        delay(5);
    }

}


void loop() {

    //ledFadeInAndOut();
    // if there are incoming bytes available

    // from the server, read them and print them:

    while (client.available()) {

        char c = client.read();

        Serial.write(c);        
    }

    // if the server's disconnected, stop the client:

    if (!client.connected()) {

        Serial.println();

        Serial.println("disconnecting from server.");

        client.stop();

        // do nothing forevermore:

        while (true);

    }

}