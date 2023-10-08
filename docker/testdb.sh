apt update -y
apt install mariadb-client -y
mysql -u $DB_USERNAME -h $DB_HOST -p