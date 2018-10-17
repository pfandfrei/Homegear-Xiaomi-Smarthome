## MiCtrlLn1

Description: Wall Switch (With Neutral, Single Rocker)

Device ID: 0x28c2

Model: ctrl_ln1 ctrl_ln1.aq1

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

## Channel 1 

### Variables

#### STATE

Wall switch status.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Boolean                   |
| Readable      | Yes                            |
| Writeable     | Yes                           |

#### PRESS_SHORT

Set if button was pressed short

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

