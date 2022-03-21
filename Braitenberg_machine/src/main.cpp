#include <Arduino.h>


#define MOT2CW 6
#define MOT2CCW 7

#define MOT1CW 8
#define MOT1CCW 9

#define MOT2PWN 4

#define MOT1PWN 5



void setup() {
  // Mettre tous les pins qu'on aura besoin en output
  // Pas besoin de définite le mode des pins PWN
  pinMode(MOT2CW,OUTPUT);
  pinMode(MOT2CCW,OUTPUT);
  pinMode(MOT1CW,OUTPUT);
  pinMode(MOT1CCW,OUTPUT);

  // Allumer le premier moteur et lui donner une vitesse avec le PWN
  digitalWrite(MOT2CW,LOW);
  digitalWrite(MOT2CCW,HIGH);
  analogWrite(MOT2PWN,255);
  
  // désactiver le deuxième moteur
  digitalWrite(MOT1CW,LOW);
  digitalWrite(MOT1CCW,LOW);
  analogWrite(MOT1PWN,0);
    
}

void loop() {
  


}