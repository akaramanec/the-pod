FROM yiisoftware/yii2-php:7.4-apache

WORKDIR /app

RUN apt update && \
    apt -y upgrade

RUN apt-get update && \
    apt-get -y upgrade
#    apt-get -y install --reinstall systemd && \
#    apt-get -y install cron && \
#    systemctl enable cron  \
    #    xdg-utils \
RUN apt-get -y install cron \
#    xdg-utils \
    wget \
    nano


RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

COPY ./init.sh /

RUN chmod +x /init.sh

CMD /init.sh

EXPOSE 8001
EXPOSE 8002

RUN chown 1000:1000 /app

ENTRYPOINT ["/init.sh"]