## Mi86Sw2

Description: Wireless Remote Switch (Double Rocker)

Device ID: 0x28d1

Model: 86sw2 sensor_86sw2 sensor_86sw2.aq1 remote.b286acn01

## Channel 0 

### Device Configuration Parameters

#### SID

Unique Device ID

|  |  |
| -------------- | ------ |
| **Type**      | String |
| **Default Value** |   |

#### HEARTBEAT

Timestamp of last heartbeat message

|                   |         |
| ----------------- | ------- |
| **Type**          | Integer |
| **Default Value** |         |
### Variables

#### VOLTAGE

Battery voltage, measured in mV units, range 0~3300mV. Under normal circumstances, less than 2800mV is considered to be low battery power.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Integer                    |
| Readable      | Yes                            |
| Writeable     | No                            |
| Unit | mV                            |
#### LOWBAT

Set to true if battery voltage is less than 2800mV.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Boolean                    |
| Readable      | Yes                            |
| Writeable     | No                            |

## Channel 1

### Variables

#### PRESS_SHORT

Set if left button was pressed short

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### PRESS_DOUBLE

Set if left button was double pressed

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |
## Channel 2

### Variables

#### PRESS_SHORT

Set if right button was pressed short

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### PRESS_DOUBLE

Set if right button was double pressed

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |
## Channel 3

### Variables

#### PRESS_BOTH

Set if left and right button was pressed together

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### 
