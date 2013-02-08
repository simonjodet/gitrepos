#!/bin/sh
echo mysql-server mysql-server/root_password select "vagrant" | debconf-set-selections
echo mysql-server mysql-server/root_password_again select "vagrant" | debconf-set-selections
apt-get install -y mysql-server apache2 php5 libapache2-mod-php5 php5-mysql
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
mysql -u root -p"vagrant" -e ";DROP DATABASE test;DROP USER ''@'localhost';CREATE DATABASE gitrepos;GRANT ALL ON gitrepos.* TO gitrepos@localhost IDENTIFIED BY 'gitrepos';GRANT ALL ON gitrepos.* TO gitrepos@'%' IDENTIFIED BY 'gitrepos'"
sed -i 's/127.0.0.1/0.0.0.0/g' /etc/mysql/my.cnf
service mysql restart
apt-get clean