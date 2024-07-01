#!/bin/sh

if [ ! -f /.build_done ]; then
    echo "Build Frontend..."
    apk add --no-cache npm
    cd /app

    npm cache clean --force
    npm install -g @quasar/cli
    npm install

    echo "Building stage..."
    quasar build

    touch /.build_done
fi

exec nginx -g "daemon off;"
