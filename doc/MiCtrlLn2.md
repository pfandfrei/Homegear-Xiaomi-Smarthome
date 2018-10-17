## MiCtrlLn2

Description: Wall Switch (With Neutral, Double Rocker)

Device ID: 0x28d2

Model: ctrl_ln2 ctrl_ln2.aq1

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

Wall switch status left output.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Boolean                   |
| Readable      | Yes                            |
| Writeable     | Yes                           |

#### PRESS_SHORT

Set if left button was pressed short

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

## Channel 2

### Variables

#### STATE

Wall switch status right output.

|           |                     |
| -------------- | :----------------------------- |
| Type          | Boolean                   |
| Readable      | Yes                            |
| Writeable     | Yes                           |

#### PRESS_SHORT

Set if right button was pressed short

|           |                     |
| -------------- | :----------------------------- |
| Type          | Action |
| Readable      | No                            |
| Writeable     | No     |

