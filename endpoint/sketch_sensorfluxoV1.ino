//Lora + arduino + sensor de fluxo
//adaptação funcionando
//02/02/2019 - 16:30

#include <lmic.h>
#include <hal/hal.h>
#include <SPI.h>

static const PROGMEM u1_t NWKSKEY[16] = { 0xEB, 0x73, 0x17, 0x56, 0x16, 0x55, 0x7E, 0x08, 0xCF, 0xB5, 0x2B, 0xFD, 0x1A, 0x35, 0x74, 0xFA };

static const u1_t PROGMEM APPSKEY[16] = { 0xE1, 0xB2, 0x33, 0x5D, 0x17, 0x75, 0xDB, 0xE1, 0xA0, 0x06, 0x5A, 0x0F, 0xAF, 0x05, 0xA6, 0x8F };

static const u4_t DEVADDR = 0x26031E50; 

float fluxo;
byte sensorInterrupt = 0;  // 0 = digital pin 2
byte sensorPin       = 2;  //data sensor de fluxo
float calibrationFactor = 4.5;
volatile byte pulseCount;  
float flowRate;
unsigned int flowMilliLitres;
unsigned long totalMilliLitres;

unsigned long oldTime;

void os_getArtEui (u1_t* buf) { }
void os_getDevEui (u1_t* buf) { }
void os_getDevKey (u1_t* buf) { }

static uint8_t mydata[] = { 0,0,0,0,0,0,0,0};
static osjob_t sendjob;

const unsigned TX_INTERVAL = 60;

// Pin mapping... Change the pins according to microcontroller used

// .dio={DIO0,DIO1,DIO2}

// Pin mapping
const lmic_pinmap lmic_pins = {
    .nss = 10,
    .rxtx = LMIC_UNUSED_PIN,
    .rst = LMIC_UNUSED_PIN,
    .dio = {5, 3, 4},
};

void onEvent (ev_t ev) {
    Serial.print(os_getTime());
    Serial.print(": ");
    switch(ev) {
        case EV_SCAN_TIMEOUT:
            Serial.println(F("EV_SCAN_TIMEOUT"));
            break;
        case EV_BEACON_FOUND:
            Serial.println(F("EV_BEACON_FOUND"));
            break;
        case EV_BEACON_MISSED:
            Serial.println(F("EV_BEACON_MISSED"));
            break;
        case EV_BEACON_TRACKED:
            Serial.println(F("EV_BEACON_TRACKED"));
            break;
        case EV_JOINING:
            Serial.println(F("EV_JOINING"));
            break;
        case EV_JOINED:
            Serial.println(F("EV_JOINED"));
            break;
        case EV_RFU1:
            Serial.println(F("EV_RFU1"));
            break;
        case EV_JOIN_FAILED:
            Serial.println(F("EV_JOIN_FAILED"));
            break;
        case EV_REJOIN_FAILED:
            Serial.println(F("EV_REJOIN_FAILED"));
            break;
            break;
        case EV_TXCOMPLETE:
            Serial.println(F("EV_TXCOMPLETE (includes waiting for RX windows)"));
            if(LMIC.dataLen) {
                // data received in rx slot after tx
                Serial.print(F("Data Received: "));
                Serial.write(LMIC.frame+LMIC.dataBeg, LMIC.dataLen);
                Serial.println();
            }
            // Schedule next transmission
            os_setTimedCallback(&sendjob, os_getTime()+sec2osticks(TX_INTERVAL), do_send);
            break;
        case EV_LOST_TSYNC:
            Serial.println(F("EV_LOST_TSYNC"));
            break;
        case EV_RESET:
            Serial.println(F("EV_RESET"));
            break;
        case EV_RXCOMPLETE:
            // data received in ping slot
            Serial.println(F("EV_RXCOMPLETE"));
            break;
        case EV_LINK_DEAD:
            Serial.println(F("EV_LINK_DEAD"));
            break;
        case EV_LINK_ALIVE:
            Serial.println(F("EV_LINK_ALIVE"));
            break;
         default:
            Serial.println(F("Unknown event"));
            break;
    }
}

void do_send(osjob_t* j){
    
    dtostrf(fluxo, 5, 2, (char*)mydata);
    // Check if there is not a current TX/RX job running
    
    if (LMIC.opmode & OP_TXRXPEND) {
        Serial.println(F("OP_TXRXPEND, not sending"));
    } 
    
    else {
        // Prepare upstream data transmission at the next possible time.
        
        LMIC_setTxData2(1, mydata, strlen((char*) mydata), 0);
        Serial.println(F("Packet queued"));
        Serial.println(LMIC.freq);
        
        Serial.print("Flow rate: ");
        Serial.print(flowRate);  // Print the integer part of the variable
        Serial.print("L/min");
        Serial.println("\t");       // Print tab space

    }
}

void setup() {
    Serial.begin(9600);
    Serial.println(F("Starting"));

    pinMode(sensorPin, INPUT);
    digitalWrite(sensorPin, HIGH);

    pulseCount        = 0;
    flowRate          = 0.0;
    flowMilliLitres   = 0;
    totalMilliLitres  = 0;
    oldTime           = 0;

    attachInterrupt(sensorInterrupt, pulseCounter, FALLING);

    #ifdef VCC_ENABLE
    // For Pinoccio Scout boards
    pinMode(VCC_ENABLE, OUTPUT);
    digitalWrite(VCC_ENABLE, HIGH);
    delay(1000);
    #endif

    // LMIC init
    os_init();
    // Reset the MAC state. Session and pending data transfers will be discarded.
    LMIC_reset();
   
    // Set static session parameters. Instead of dynamically establishing a session
    // by joining the network, precomputed session parameters are be provided.
    #ifdef PROGMEM
    // On AVR, these values are stored in flash and only copied to RAM
    // once. Copy them to a temporary buffer here, LMIC_setSession will
    // copy them into a buffer of its own again.
    uint8_t appskey[sizeof(APPSKEY)];
    uint8_t nwkskey[sizeof(NWKSKEY)];
    memcpy_P(appskey, APPSKEY, sizeof(APPSKEY));
    memcpy_P(nwkskey, NWKSKEY, sizeof(NWKSKEY));
    LMIC_setSession (0x1, DEVADDR, nwkskey, appskey);
    #else
    // If not running an AVR with PROGMEM, just use the arrays directly 
    LMIC_setSession (0x1, DEVADDR, NWKSKEY, APPSKEY);
    #endif

  for (int channel=0; channel<63; ++channel) {           // set frequency by choosing the active channel (this case 914.9Mhz = channel 63)
    LMIC_disableChannel(channel);
  }
  for (int channel=64; channel<72; ++channel) {          // set frequency by choosing the active channel (this case 914.9Mhz = channel 63)
     LMIC_disableChannel(channel);
  }

    // Disable link check validation
    LMIC_setLinkCheckMode(0);
 
    // Set data rate and transmit power (note: txpow seems to be ignored by the library)
    LMIC_setDrTxpow(DR_SF7,14);

    // Start job
    do_send(&sendjob);
Serial.println("Freq");
Serial.println(LMIC.freq);
}

void loop() 
{
    os_runloop_once();
    
     if((millis() - oldTime) > 1000){ 
        detachInterrupt(sensorInterrupt);
        flowRate = ((1000.0 / (millis() - oldTime)) * pulseCount) / calibrationFactor;
        oldTime = millis();
        flowMilliLitres = (flowRate / 60) * 1000;
        totalMilliLitres += flowMilliLitres;
        pulseCount = 0;

        fluxo = flowRate;
    
        attachInterrupt(sensorInterrupt, pulseCounter, FALLING);
  }
}

void pulseCounter(){
  pulseCount++;
}
