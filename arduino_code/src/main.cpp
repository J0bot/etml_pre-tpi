#include <Arduino.h>


#include <SPI.h>
#include <WiFiNINA.h>

#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>
#include "secrets.h"


//Tout ce qui concerne le wifi
char ssid[] = SECRET_SSID;        // your network SSID (name)
char pass[] = SECRET_PASS;      // your network password (use for WPA, or use as key for WEP)
int status = WL_IDLE_STATUS;     // le status du wifi

//Variable client de la classe WiFiClient
WiFiClient client;


//Tout ce qui concerne le mysql
//IPAddress server_addr(192,168,1,36);  // IP of the MySQL *server* here
IPAddress server_addr(172,16,0,7);  // IP of the MySQL *server* here
char user[] = USER_MYSQL;              // MySQL user login username
char password[] = PASS_MYSQL;        // MySQL user login password

//L'objet conn de la classe mysql_connection fait référence à l'objet WifiClient
MySQL_Connection conn((Client *)&client);


//C'est la fonction qui nous permet d'executer une query
void executeQuery(const char mysql_query[])
{
  //Initiate the query class instance
  MySQL_Cursor *cur_mem = new MySQL_Cursor(&conn);
  // Execute the query
  cur_mem->execute(mysql_query);
  // Note: since there are no results, we do not need to read any data
  // Deleting the cursor also frees up memory used
  delete cur_mem;
}

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


    Serial.print("Pinging ");
    Serial.print("172.16.0.7");
    Serial.print(": ");
    int pingResult;
    pingResult = WiFi.ping("172.16.0.7");

    if (pingResult >= 0) {
        Serial.print("SUCCESS! RTT = ");
        Serial.print(pingResult);
        Serial.println(" ms");
    } else {
        Serial.print("FAILED! Error code: ");
        Serial.println(pingResult);
    }

    delay(5000);

    //Connection to mysql database
    Serial.println("Connecting...");
    if (conn.connect(server_addr, 3306, user, password)) {

        //Petit delay de 1 secondes pour laisser le temps de se connecter
        delay(1000);
        Serial.println("Connection successful !");
    
        char queryTest[] = "UPDATE db_arduino.t_switch SET swiDelay=45 WHERE idSwitch=1";
        executeQuery(queryTest);

        Serial.println(queryTest);

    }
    else
    {
        // Si la connection a vraiment fail on va afficher qu'elle a fail
        Serial.println("Connection failed.");
        delay(5000);
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
    

}