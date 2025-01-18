#pod
*/15 * * * * /usr/bin/php7.2 /home/pod1886/shop/yii blogger/cache-blogger-fixed
*/5 * * * * /usr/bin/php7.2 /home/pod1886/shop/yii newsletter/index
0 13 * * * /usr/bin/php7.2 /home/pod1886/shop/yii notice/index
5 9-18 * * * /usr/bin/php7.2 /home/pod1886/shop/yii np/check-status-en-np
0 5 * * * /usr/bin/php7.2 /home/pod1886/shop/yii product/img-save
40 8-20 * * * /usr/bin/php7.2 /home/pod1886/shop/yii product/index
20 3 * * * /usr/bin/php7.2 /home/pod1886/shop/yii service/clear-logger




--------------------------DEV------------------------------------------


mysql -u root -p'dQj5-2!uS?ok-516H' pod < /home/igor/www/pod-dev/tmp/pod.sql


/usr/bin/php7.2 /home/igor/www/pod/yii notice/new-version
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii notice/new-version

/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii blogger/cache-blogger-fixed
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii cron/check-pay
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii cron/product
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii notice/index
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii cron/check-status-en-np
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii cron/test
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii service/clear-logger
/usr/local/php74/bin/php -d memory_limit=-1 /home/nv397414/thepod.com.ua/shop/yii service/test


cron/check-pay
cron/product
php /home/igor/www/pod/yii notice/index






15 8-20 * * * mysqldump -u 'root' -p'Wyb3gih-jo3gkop-2bu3gnI' mh > /home/mh8512/medhause/tmp/sql/`date +\%d`.sql
mysql -u root -p'DhU7dl_ld88*8&3h*jslp-3kso_UswAG' shop < /home/pod1886/dev/tmp/nv397414_bot.sql   #загрузить дамп









