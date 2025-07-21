#!/bin/bash

# Start Apache
service apache2 start
echo "Apache started"
echo "cake migrations migrate"
bin/cake migrations migrate
# Run cake commands
/var/www/html/bin/cake Processrcvq &
/var/www/html/bin/cake Processsendq &
/var/www/html/bin/cake Metrics &

CMD tail -f /dev/null