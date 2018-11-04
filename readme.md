## Xiaomi Smart Home - Homegear Interface
V0.6.5 supports firmware version 1.4.1_159.0143

supports Homegear 0.7.x 

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
4. Restart homegear (sudo service homegear restart)
5. if you have changed homegear default path, you have to use the customized paths instead
6. run 'homegear -e rs DeviceScripts/Xiaomi/MiSmartHome.php'
7. the script will autodiscover all available Xiaomi devices (see supported devices below) and auto-create Homegear devices
8. add the password from step 1.10 for the gateway device: `homegear -e rc '$hg->putParamset(<gateway peer id>,0,["PASSWORD"=>"<enter password>"]);'`

# Supported devices (tested)

## more detailed device documentation can be found in the doc folder

**[Xiaomi Mi Smart Home Gateway 2](https://xiaomi-mi.com/mi-smart-home/xiaomi-mi-gateway-2/) (MiGateway)**

- illumination (read): ambient light, values are from 300 to 1300
- rgb led light (read/write): use parameter RGB for color (0-255 for each color) and BRIGHTNESS (0-100%) for lamp brightness
- parameter ENABLE will turn rgb light on/off with last values for RGB/BRIGHTNESS
- parameter MUSIC_ID can be used to select preset sound or mp3 uploaded by user (MUSIC_ID >=10001)
- parameter VOLUME will set the volume for the music to play
- parameter PLAY will start/stop playing the selected music
- multiple gateways are supported

**[Xiaomi Mi Smart Home Temperature/Humidity Sensor](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-temperature-humidity-sensor/) (MiSensorHT)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- HUMIDITY shows curret humidity in %. It will be updated when value changes by more than 0,5%
- TEMPERATURE shows current temperature in °C. It will be updated when value changes by more than 0,5°C

**[Xiaomi Aqara Temperature Humidity Sensor](https://xiaomi-mi.com/sockets-and-sensors/aqara-temperature-and-humidity-sensor/) (MiWeatherV1)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- HUMIDITY shows curret humidity in %. It will be updated when value changes by more than 0,5%
- TEMPERATURE shows current temperature in °C. It will be updated when value changes by more than 0,5°C
- PRESSURE shows current pressure 

**[Xiaomi Mi Smart Home Wireless Switch](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-wireless-switch/) (MiSwitch)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- PRESS_SHORT is set when button is clicked 
- PRESS_LONG is set when button is longer pressed and hold 
- PRESS_LONG_RELEASE is set when longer pressed button is released
- PRESS_DOUBLE is set when button is double clicked
  
**[Xiaomi Mi Smart Home Door/Window Sensor](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-door-window-sensors/) (MiMagnet)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- STATE (OPEN: sensor is opened, CLOSED: sensor is closed, UNKNOWN: state is not known e.g. after power on)

**[Xiaomi Mi Smart Home Occupancy Sensor](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-occupancy-sensor/) (MiMotion)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- MOTION is set true if motion is detected. Value is set not more than once a minute

**[Xiaomi Aqara Window Door Sensor](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-aqara-window-door-sensor/) (MiMagnetAq2)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- STATE (OPEN: sensor is opened, CLOSED: sensor is closed, UNKNOWN: state is not known e.g. after power on)

**[Xiaomi Smart Home Aqara Human Body Sensor](https://xiaomi-mi.com/sockets-and-sensors/aqara-human-body-sensor/) (MiMotion)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- MOTION is set true if motion is detected. Value is set not more than once a minute
- ILLUMINATION ambient light

**Aqara Vibration Sensor (MiVibration)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- VIBRATE is set true if vibration is detected. Value is set not more than once a minute
- FREE_FALL is set true if free fall (drop of more than 7cm) is detected. Value is set not more than once a minute
- TILT is set true if tilt is detected. Value is set not more than once a minute

**[Xiaomi Mi Smart Home Cube](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-smart-home-cube-white/) (MiCube)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- FLIP90 is set when cube is flipped by one side (90 degrees)
- FLIP180 is set when cube is flipped by two sides (180 degrees)
- MOVE is set when cube is moved
- TAP_TWICE is set when cube is double tapped 
- SHAKE AIR tbd 
- SWING tbd
- ALERT tbd 
- FREE_FALL tbd


## untested, but should work ##
**[Xiaomi Aqara Smart Light Control](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-aqara-smart-light-control-set/) (Mi86Sw1/Mi86Sw2)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- PRESS_SHORT is set when button is clicked 
- PRESS_DOUBLE is set when button is double clicked
- PRESS_BOTH is set when lboth buttons are clicked at same time (MiSw2 only)

**[Xiaomi Mi Smart Socket Plug](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mi-smart-socket-plug/) (MiPlug)**
- STATE (ON: socket is switched on, OFF: socket is switched off, UNKNOWN: state is not known e.g. after power on)
- LOAD_VOLTAGE socket voltage in mV
- LOAD_POWER actually consumed power in W
- POWER_CONSUMED sum of consumed electrical power in kWh

**Xiaomi Aqara Wall Outlet (Mi86Plug)**

- STATE (ON: socket is switched on, OFF: socket is switched off)
- LOAD_POWER actually consumed power in W
- POWER_CONSUMED sum of consumed electrical power in kWh

**[Xiaomi Aqara Water Leak Sensor](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mijia-aqara-water-sensor/) (MiWLeakAq1)**

- LEAK is set to true if water is detected

**[Xiaomi MiJia Honeywell Smoke Detector](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mijia-honeywell-smoke-detector-white/) (MiSmoke)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- ALARM (Release alarm/Fire alarm/Analog alarm/Battery fault alarm/Sensitivity fault alarm/IIC communication failure)

**[Xiaomi MiJia Honeywell Natural Gas Detector](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-mijia-honeywell-gas-leak-detector-white/) (MiNatGas)**

- ALARM (Release alarm/Fire alarm/Analog alarm/Sensitivity fault alarm/IIC communication failure)

**[Xiaomi Aqara Smart Wireless Switch](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-aqara-smart-wireless-switch/) (MiSwitchAq2)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- PRESS_SHORT is set when button is clicked 
- PRESS_LONG is set when button is longer pressed and hold 
- PRESS_LONG_RELEASE is set when longer pressed button is released
- PRESS_DOUBLE is set when button is double clicked

**Xiaomi Aqara Smart Wireless Switch (MiSwitchAq3)**

- VOLTAGE shows current battery voltage in mV. Values lower than 2800mV indicates a low battery
- PRESS_SHORT is set when button is clicked 
- PRESS_LONG is set when button is longer pressed and hold 
- PRESS_LONG_RELEASE is set when longer pressed button is released
- PRESS_DOUBLE is set when button is double clicked
- SHAKE is set when button is moved (shaked)
  
## not implemented ##

**[Xiaomi Aqara Smart Curtain Controller](https://xiaomi-mi.com/sockets-and-sensors/xiaomi-aqara-smart-curtain-controller-white/)**

## how to enable debug mode ##
for support of unknown devices and other problems you can enable debug log by setting the config parameter DEBUG_LEVEL of gateway device to 1.
A separate log is created at /var/log/homegear/mihome.log

