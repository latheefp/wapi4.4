#!/usr/bin/env bash
DB_PASS="rootPassword"
DB_USERNAME="root"
DB_HOST=192.168.8.191
DB_DATABASE="waapi"
SEND_MSG=1
LOG=1
ENVIRONMENT="DEV"
export INTERACTIV_LOGS="1"
RCVQRUN=0
SNDQRUN=0
CHAT_URL="ws://localhost:8080"
CHAT_INTERNAL_URL="ws://localhost:8080"
REDIS_SERVER=redis
REDIS_PORT="6379"
WSENABLED=1
METRICSENABLED=0
#export DATABASE_URL="mysql://root:rootPassword@db/waapi?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"
#export DATABASE_URL="mysql://my_app:secret@localhost/${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"
#export DATABASE_TEST_URL="mysql://my_app:secret@localhost/test_${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"