cp api.php /var/www/html/
cp getmatches.php /var/www/html/
cp index.php /var/www/html/

cp -R conf/ /var/www/html/
cp -R objects/ /var/www/html/
cp -R Rest/ /var/www/html/
cp -R tools/ /var/www/html/
cp -R utils/ /var/www/html/
cp -R Test/ /var/www/html/

#touch /tmp/mooov.log
if [ "$1" = "dev" ]; then
 cp conf/dev.conf /etc/apache2/sites-available/default
else
 cp conf/apache.conf /etc/apache2/sites-available/default
fi

