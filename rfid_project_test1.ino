#include <ESP8266WiFi.h>
#include <MFRC522.h>
#include <SPI.h>
#include <ArduinoHttpClient.h>

// WiFi Credentials
const char* ssid = "";  // Replace with your WiFi SSID
const char* password = "";  // Replace with your WiFi Password

// Server Details
const char* serverAddress = "";  // Your PHP server IP
const int serverPort = 8080;
const char* phpScript = "/students_attendance.php";

// RFID Module Pins
#define SS_PIN  4  // SDA pin of RFID (D2 on NodeMCU)
#define RST_PIN 5  // RST pin of RFID (D1 on NodeMCU)

MFRC522 rfid(SS_PIN, RST_PIN);
WiFiClient wifiClient;
HttpClient client = HttpClient(wifiClient, serverAddress, serverPort);

void setup() {
    Serial.begin(115200);
    SPI.begin();
    rfid.PCD_Init();
    
    // Connect to WiFi
    Serial.print("Connecting to WiFi...");
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
}

void loop() {
    if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
        return; // No card detected
    }

    // Read UID from RFID card
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    
    Serial.print("Card UID: ");
    Serial.println(uid);
    
    // Send data to server
    sendAttendance(uid, "Present"); 
    
    delay(2000); // Delay before next scan
}

void sendAttendance(String uid, String status) {
    // Construct URL
    String url = String(phpScript) + "?uid=" + uid + "&status=" + status;
    Serial.print("Sending request to: ");
    Serial.println(url);

    client.get(url);

    int statusCode = client.responseStatusCode();
    String response = client.responseBody();

    Serial.print("HTTP Response Code: ");
    Serial.println(statusCode);
    
    if (statusCode == 200) {
        Serial.println("Server Response: " + response);
    } else {
        Serial.println("Error sending request.");
    }

    client.stop();
}
