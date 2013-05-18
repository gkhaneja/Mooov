mysql -Nse 'show tables' hopon -p | while read table; do mysql -e "truncate table $table" hopon -p ; done
