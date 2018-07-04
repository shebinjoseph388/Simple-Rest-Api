FROM ubuntu:16.04
MAINTAINER shebin joseph <shebinjoseph388@gmail.com>

#UpdateRepository
RUN apt-get install -y apache2

#Install PHP module
RUN apt-get install -y php7.0 libapache2-mod-php7.1 php7.1-cli php7.1-common php7.1-mbstring php7.1-gd php7.1-intl php7.1-xal php7.1-mysql php7.1-mcrypt php7.1-zip

#Copy Application files
RUN rm -rf /var/www/html/*
ADD SIMPLE-REST-API /var/www/html

#Configure Apache (Optional)
RUN chown -R www-data:www-data /var/www
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
#Open port 80
EXPOSE 80 

#Start apache service
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"] 