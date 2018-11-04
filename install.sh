#!/bin/bash
if [ "$(id -u)" != "0" ]; then
    echo "This script must be run as root" 1>&2
    exit 1
fi

HOMEGEAR_DIR=/var/lib/homegear/scripts/DeviceScripts/Xiaomi
SCRIPT_DIR="$( cd "$(dirname $0)" && pwd )"

cp "$SCRIPT_DIR/Mi*.xml" /etc/homegear/devices/254/

mkdir -p "$HOMEGEAR_DIR"
cp "$SCRIPT_DIR/Mi*.php" "$HOMEGEAR_DIR"
chown -R homegear:homegear "$HOMEGEAR_DIR"
