#!/usr/bin/env bash
DB_PASS="rootPassword"
DB_USERNAME="root"
DB_HOST="db"
DB_DATABASE="waapi"
SEND_MSG=0
LOG=1
ENVIRONMENT="DEV"
export INTERACTIV_LOGS="1"
RCVQRUN=10
SNDQRUN=0
#export DATABASE_URL="mysql://root:rootPassword@db/waapi?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"
#export DATABASE_URL="mysql://my_app:secret@localhost/${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"
#export DATABASE_TEST_URL="mysql://my_app:secret@localhost/test_${APP_NAME}?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false"