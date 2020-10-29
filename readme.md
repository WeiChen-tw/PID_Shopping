# 購物網專案
## 安裝 apache2
sudo apt install apache2<br>
## 安裝 php
sudo apt install php libapache2-mod-php php-mysql<br>
## 安裝 mysql 8.0
到官方網站下載<br>
https://dev.mysql.com/downloads/repo/apt/<br>
點選Download 跳轉後 點選左下角 No thanks,just start my download.<br>
下載後在存放目錄執行 dpkg 指令安裝<br>
sudo dpkg -i mysql-apt-conifg_0.8.13-1_all.deb<br>
config 設定選擇 Mysql 8.0 版本 然後選擇 OK <br>
sudo apt-get update<br>
初始化mysql <br>
mysql_secure_installation <br>
## 安裝 vim
sudo apt-get remove vim-common
sudo apt-get install vim
## 安裝 composer 並安裝專案套件
在PID_Shopping-master專案目錄執行指令安裝composer
php -r "readfile('https://getcomposer.org/installer');" | php <br>
執行指令使用composer 安裝專案套件 <br>
composer.phar install
## 設定 msyql 建立homestead資料庫
登入 mysql 輸入密碼
mysql -u root -p
輸入mysql 指令 
create database homestead;
成功後離開msyql
quit
## 到PID_Shopping-master專案目錄執行
php artisan migrate

## 設定ubuntu hosts  nano
nano /etc/hosts
### 加入以下兩行
127.0.0.1	www.admin.net <br>
127.0.0.1	www.shopping.net <br>
## apache2 設置多站點
複製PID_Shopping-master專案目錄下的 apache2_config 資料夾內的設定檔到 apache2 資料夾
cd PID_Shopping-master/apache2_config
cp www-admin-net.conf www-shopping-net.conf  /etc/apache2/sites-available
執行指令啟動設定
sudo a2ensite www-admin-net.conf 
sudo a2ensite www-shopping-net.conf 
重啟apache2
sudo service apache2 restart
## 測試專案是否正常開啟
後台網址:www.admin.net
後台帳號:admin@g.com.tw
後台密碼:123456

前台網址:www.shopping.net


