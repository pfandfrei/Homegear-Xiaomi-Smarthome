## Mi86Plug

Description: Wall outlet

Device ID: 0x28f1

Model: 86plug ctrl_86plug ctrl_86plug.aq1

## Channel 0 

### Device Configuration Parameters

#### SID

Unique Device ID

|  |  |
| -------------- | ------ |
| Type      | String |
| Default Value |   |

#### HEARTBEAT

Timestamp of last heartbeat message

|                   |         |
| ----------------- | ------- |
| Type          | Integer |
| Default Value |         |

## Channel 1

### Variables

#### STATE

Defines weather plug is switched on or off

|           |                     |
| -------------- | :----------------------------- |
| Type          | Enumeration                    |
| Readable      | Yes                            |
| Writeable     | Yes                            |
| Default Value | 0                              |
| Values        | 0 OFF<br />1 ON |

#### LOAD_POWER

Load power in watts (W)

|           |       |
| --------- | :---- |
| Type      | Float |
| Readable  | Yes   |
| Writeable | No    |
| Unit      | W     |

#### POWER_CONSUMED

The cumulative load power consumption since the product was used, in kilowatt-hours (kWh)

|           |       |
| --------- | :---- |
| Type      | Float |
| Readable  | Yes   |
| Writeable | No    |
| Unit      | kWh   |

