#!/bin/bash

# Start Apache
service apache2 start

# Run cake commands
/var/www/html/bin/cake Processrcvq &
/var/www/html/bin/cake Processsendq &
 
 tail -f /dev/null