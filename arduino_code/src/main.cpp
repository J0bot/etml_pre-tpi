// Import required libraries
#include <SPI.h>
#include <WiFiNINA.h>


#include <aREST.h>

#define RED 5
#define GREEN 4
#define BLUE  3
#define echoPin 7
#define trigPin 6

// Create aREST instance
aREST rest = aREST();

char ssid[] = "NETGEAR96";      // your network SSID (name)
char pass[] = "freshviolin032";   // your network password
int keyIndex = 0;                 // your network key Index number (needed only for WEP)

int status = WL_IDLE_STATUS;

// defines variables
long duration; // variable for the duration of sound wave travel
int distance; // variable for the distance measurement

WiFiServer restServer(80);

// Custom function accessible by the API
int ledControl(String command) {

    Serial.println(command);
    
    // Get state from command
    int state = command.toInt();

    digitalWrite(6,state);


    delay(state);


    return 1;
}

int startMotor(String command){
    digitalWrite(0,HIGH);
    return 0;
}

int stopMotor(String command){
    digitalWrite(0,LOW);
    return 0;
}



int getDistance(String command){
    // Clears the trigPin condition
    digitalWrite(trigPin, LOW);
    delayMicroseconds(2);
    // Sets the trigPin HIGH (ACTIVE) for 10 microseconds
    digitalWrite(trigPin, HIGH);
    delayMicroseconds(10);
    digitalWrite(trigPin, LOW);
    // Reads the echoPin, returns the sound wave travel time in microseconds
    duration = pulseIn(echoPin, HIGH);
    // Calculating the distance
    distance = duration * 0.034 / 2; // Speed of sound wave divided by 2 (go and back)
    // Displays the distance on the Serial Monitor
    Serial.print("Distance: ");
    Serial.print(distance);
    Serial.println(" cm");

    return distance;
}

void printWifiStatus() {
    // print the SSID of the network you're attached to:
    Serial.print("SSID: ");
    Serial.println(WiFi.SSID());

    // print your WiFi shield's IP address:
    IPAddress ip = WiFi.localIP();
    Serial.print("IP Address: ");
    Serial.println(ip);

    IPAddress subnet = WiFi.subnetMask();
    Serial.print("Netmask: ");
    Serial.println(subnet);

    IPAddress gateway = WiFi.gatewayIP();
    Serial.print("Gateway: ");
    Serial.println(gateway);

    // print the received signal strength:
    long rssi = WiFi.RSSI();
    Serial.print("signal strength (RSSI):");
    Serial.print(rssi);
    Serial.println(" dBm");
}




void setup(void)
{

    pinMode(0,OUTPUT);
    pinMode(1,INPUT);

    pinMode(trigPin, OUTPUT); // Sets the trigPin as an OUTPUT
    pinMode(echoPin, INPUT); // Sets the echoPin as an INPUT

    // Function to be exposed
    rest.function("led",ledControl);
    rest.function("distance",getDistance);
    rest.function("start_motor",startMotor);
    rest.function("stop_motor",stopMotor);
    // Give name and ID to device
    rest.set_id("008");
    rest.set_name("arduinou");

    // Start Serial
    Serial.begin(9600);

    //while (!Serial) {
        ; // wait for serial port to connect. Needed for native USB port only
    //}

    // check for the presence of the shield:
    if (WiFi.status() == WL_NO_SHIELD) {
        Serial.println("WiFi shield not present");
        // don't continue:
        while (true);
    }

    // attempt to connect to Wifi network:
    while ( status != WL_CONNECTED) {
        Serial.print("Attempting to connect to SSID: ");
        Serial.println(ssid);
        // Connect to WPA/WPA2 network. Change this line if using open or WEP network:
        status = WiFi.begin(ssid, pass);

        // wait 10 seconds for connection:
        delay(5000);
    }

    Serial.println();

    // you're connected now, so print out the status:
    printWifiStatus();

    // Start server
    restServer.begin();
    Serial.println(F("Listening for connections..."));

    // Enable watchdog
    //wdt_enable(WDTO_4S);
}

void loop() {

    // Handle REST calls
    WiFiClient client = restServer.available();
    rest.handle(client);

}


