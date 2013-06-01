#mkdir -p /var/www/html

cp api.php /var/www/html/
#cp getmatches.php /var/www/html/

cp -R conf/ /var/www/html/
cp -R objects/ /var/www/html/
cp -R Rest/ /var/www/html/
cp -R tools/ /var/www/html/
cp -R utils/ /var/www/html/
cp -R Test/ /var/www/html/

touch /tmp/mooov.log
chmod 644 /tmp/mooov.log
chown www-data:www-data /tmp/mooov.log

if [ "$1" = "dev" ]; then
 cp conf/dev.conf /etc/apache2/sites-available/default
 cp conf/devdb.inc /var/www/html/conf/db.inc
else
 cp conf/apache.conf /etc/apache2/sites-available/default
fi

/etc/init.d/apache2 restart
