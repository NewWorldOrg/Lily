#!/bin/sh
set -e

CONTROL_SOCKET="/var/run/control.unit.sock"
STATE_DIR="/var/lib/unit"

load_configs() {
    for file in /docker-entrypoint.d/*.json; do
        [ -f "$file" ] || continue
        echo "docker-entrypoint: applying $file"
        result="$(curl -s -X PUT --data-binary "@$file" --unix-socket "$CONTROL_SOCKET" http://localhost/config)"
        echo "$result"
        if echo "$result" | grep -q '"error"'; then
            echo "docker-entrypoint: failed to apply $file" >&2
            return 1
        fi
    done
}

if { [ "$1" = "unitd" ] || [ "$1" = "unitd-debug" ]; } && [ "$2" != "--version" ] && [ "$2" != "-h" ]; then
    if [ -z "$(ls -A "$STATE_DIR" 2>/dev/null)" ]; then
        echo "docker-entrypoint: first start, loading initial configuration"
        rm -f "$CONTROL_SOCKET"
        "$1" --control "unix:$CONTROL_SOCKET" --pid /var/run/unit-init.pid --log /dev/stderr --statedir "$STATE_DIR"
        for _ in $(seq 1 50); do
            [ -S "$CONTROL_SOCKET" ] && break
            sleep 0.1
        done
        load_configs
        kill -TERM "$(cat /var/run/unit-init.pid)"
        for _ in $(seq 1 50); do
            [ -f /var/run/unit-init.pid ] || break
            sleep 0.1
        done
        rm -f "$CONTROL_SOCKET"
        echo "docker-entrypoint: initial configuration loaded"
    else
        echo "docker-entrypoint: state directory is not empty, skipping initial configuration"
    fi
fi

exec "$@"
