## MiCube

Description: 

Device ID: 0x28bc

Model: cube sensor_cube sensor_cube.aqgl01

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

#### FLIP90

Set if cube was flipped by 90 degrees

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### FLIP180

Set if cube was flipped by 180 degrees

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### MOVE

Set if cube was moved

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### TAP_TWICE

Set if cube was tapped two times

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### SHAKE_AIR

Set if cube was shaked

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### SWING

Set if cube was swinged

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### ALERT

???

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### FREE_FALL

Set if cube falls down at least about 7cm

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

## Channel 2

### Variables

#### ROTATE

Contains the angle the cube was roteted. Negative values for counterwise rotation, positive values for clockwise rotation.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Integer |
| Readable      | Yes                        |
| Writeable     | No     |
| Unit     | deg     |

#### TIME

Contains the time in milliseconds needed when cube was roteted.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Integer |
| Readable      | Yes                        |
| Writeable     | No     |
| Unit     | ms     |

#### ROTATE_RIGHT

Set if cube was rotated right

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

#### ROTATE_LEFT

Set if cube was rotated left

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No |
