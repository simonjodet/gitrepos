#!/bin/sh
apt-get install -y apache2 php5 libapache2-mod-php5 php5-sqlite
VHOST=$(cat <<EOF
<VirtualHost *:80>
  DocumentRoot "/vagrant/web"
  ServerName localhost
  <Directory "/vagrant/web">
    AllowOverride All
  </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-enabled/000-default
sudo a2enmod rewrite
service apache2 restart