#!/bin/bash

# Start Apache
service apache2 start

# Run cake commands
/var/www/html/bin/cake Processrcvq &
/var/www/html/bin/cake Processsendq &
/var/www/html/bin/cake Metrics &
/var/www/html/bin/cake  chat -a start &
 
 tail -f /dev/null