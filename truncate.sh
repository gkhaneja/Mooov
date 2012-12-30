mysql -Nse 'show tables' test | while read table; do mysql -e "truncate table $table" test; done
