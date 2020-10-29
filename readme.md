# 購物網專案
## [環境安裝文件](https://docs.google.com/presentation/d/15EFSNWqlMVlAhhywFbNxTK3VIgTWckzQwpQSpE94peE/edit?usp=sharing)
## 安裝 apache2
sudo apt install apache2<br>
## 安裝 php7.2
sudo apt install php7.2 libapache2-mod-php7.2 php7.2-mysql php7.2-gd php7.2-intl php7.2-xml php7.2-mbstring<br>
## 安裝 mysql 8.0
到官方網站下載<br>
https://dev.mysql.com/downloads/repo/apt/<br>
點選Download 跳轉後 點選左下角 No thanks,just start my download.<br>
下載後在存放目錄執行 dpkg 指令安裝<br>
sudo dpkg -i mysql-apt-conifg_0.8.15-1_all.deb<br>
config 設定選擇 Mysql 8.0 版本 然後選擇 OK 按下 Enter<br>

sudo apt-get update<br>
sudo apt-get install mysql-server<br>
畫面會要求你設定Mysql root 帳號的密碼 輸入password <br>

畫面詢問你密碼難度規定 我們使用第二個選項 按下Enter<br>
選擇 Use Legacy Authentication Method (Retain Mysql 5.x Compatibility)<br>


## 安裝 composer 並安裝專案套件
在PID_Shopping-master專案目錄執行指令安裝composer<br>
php -r "readfile('https://getcomposer.org/installer');" | php <br>
將檔案移動到 usr/local/bin<br>
sudo mv composer.phar /usr/local/bin/<br>
在PID_Shopping-master專案目錄執行指令使用composer 安裝專案套件 <br>
composer.phar install<br>
## 到PID_Shopping-master專案目錄執行 （user 請換成自己ubuntu的userName)
sudo chown -R user:www-data storage/<br>
## 設定 msyql 建立homestead資料庫
在PID_Shopping-master專案目錄裡的 database_config<br>
cd PID_Shopping-master/database_config<br>
執行登入並匯入資料庫指令後要輸入password(Mysql root 密碼 輸入密碼會看不到字是正常的)  <br>
mysql -u root -p < homestead.sql<br>


## 設定ubuntu hosts  
sudo nano /etc/hosts<br>
加入以下兩行<br>
127.0.0.1	www.admin.net <br>
127.0.0.1	www.shopping.net <br>
輸入指令離開檔案編輯
ctrl + X 輸入 Y 按下 Enter
## apache2 設置多站點
在PID_Shopping-master專案目錄下的 apache2_config 資料夾內編輯設定檔<br>
sudo nano www-admin-net.conf<br>

sudo nano www-shopping-net.conf<br>

複製設定檔到 apache2 資料夾<br>
cd PID_Shopping-master/apache2_config <br>
sudo cp www-admin-net.conf www-shopping-net.conf  /etc/apache2/sites-available <br>
執行指令啟動設定<br>
sudo a2ensite www-admin-net.conf www-shopping-net.conf<br>
啟動Rewrite模組 <br>
sudo a2enmod rewrite<br>

重啟apache2<br>
sudo service apache2 restart <br>
## 測試專案是否正常開啟
後台網址:www.admin.net
後台帳號:admin@g.com.tw
後台密碼:123456<br>

前台網址:www.shopping.net


