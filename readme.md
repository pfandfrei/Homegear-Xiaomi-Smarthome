## Xiaomi Smart Home - Homegear Interface
V0.2.0 - (untested - no hardware) support ctrl_neutral1, ctrl_neutral2, 86sw1, 86sw2
# Step 1: bring Xiaomi Gateway in developer mode #

1. Install the MiHome app on your mobile phone
2. Set region to: Mainland China under settings > Locale
3. You can set language to English
4. Select your Gateway in MiHome
5. Click the 3 dots at the top right of the screen
6. Click on about
7. Tap the version number at the bottom of the screen repeatedly
8. Two extra options will appear in English (in Chinese in earlier versions) until you did now enable the developer mode
9. Choose the first new option
10. Then tap the first toggle switch to enable LAN functions. Write down the password (Needed in Homegear)
11. Make sure you hit the OK button to save the  changes
12. Be aware: Every time you change something, the password will change!

All Xiaomi devices have to be added in MiHome app.

# Step 2: install Xiaomi devices #
1. make a ssh connection to homegear device
2. copy all *.xml files to /etc/homegear/devices/254/
3. copy all *.php files to /var/lib/homegear/scripts/DeviceScripts/Xiaomi/
4. if you have changed homegear default path, you have to use the customized paths instead
5. run 'homegear -e rs DeviceScripts/Xiaomi/MiSmartHome.php'
6. the script will autodiscover all available Xiaomi devices (see supported devices below) and auto-create Homegear devices
7. add the password from step 1.10 for the gateway device: `homegear -e rc '$hg->putParamset(<gateway peer id>,0,["PASSWORD"=>"<enter password>"]);'`

# Supported devices (tested)

**gateway v2 (MiGateway)**

- illumination (read): ambient light, values are from 300 to 1300
- rgb led light (read/write): use parameter RGB for color (0-255 for each color) and BRIGHTNESS (0-100%) for lamp brightness
- parameter ENABLE will turn rgb light on/off with last values for RGB/BRIGHTNESS
- parameter MUSIC_ID can be used to select preset sound or mp3 uploaded by user (MUSIC_ID >=10001)
- parameter VOLUME will set the volume for the music to play
- parameter PLAY will start/stop playing the selected music
- multiple gateways should be supported but implentation is untested

**sensor ht (MiSensorHT)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- HUMIDITY shows curret humidity in %. It will be updated when value changes by more than 0,5%
- TEMPERATURE shows current temperature in °C. It will be updated when value changes by more than 0,5°C

**switch (MiSwitch)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- PRESS_SHORT is set when button is clicked 
- PRESS_LONG is set when button is longer pressed and hold 
- PRESS_LONG_RELEASE is set when longer pressed button is released
- PRESS_DOUBLE is set when button is double clicked
  
## all other devices are untested until now, because I do not own these devices ##
**cube (MiCube)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- FLIP90 is set when cube is flipped by one side (90 degrees)
- FLIP180 is set when cube is flipped by two sides (180 degrees)
- MOVE is set when cube is moved
- TAP_TWICE is set when cube is double tapped 
- SHAKE AIR tbd 
- SWING tbd
- ALERT tbd 
- FREE_FALL tbd

**door sensor (MiMagnet)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- STATE (OPEN: sensor is opened, CLOSED: sensor is closed, UNKNOWN: state is not known e.g. after power on)

**occupancy sensor (MiMotion)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- MOTION is set true if motion is detected. Value is set not more than once a minute

*wall switches, gas/smoke sensors are currently not implemented/will be implemented later*

## how to enable debug mode ##
for support of unknown devices and other problems you can enable debug log by setting the config parameter DEBUG_LEVEL of gateway device to 1.
A separate log is created at /var/log/homegear/mihome.log

