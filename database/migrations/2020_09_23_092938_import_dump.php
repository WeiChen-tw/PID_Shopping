<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class ImportDump extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(file_get_contents('/shopping/database/homestead.sql'))
        // DB::unprepared('
        //     CREATE TABLE `userDetail`(id int(10) ZEROFILL NOT NULL ,account varchar(30),orderID int(10) ZEROFILL NOT NULL,datatime timestamp  DEFAULT CURRENT_TIMESTAMP,actionName varchar(30),price int(30) UNSIGNED,status varchar(30),sellerID int(10) ZEROFILL NOT NULL);
        //     CREATE TABLE `product` (id int(10) ZEROFILL NOT NULL,productID int(10) ZEROFILL NOT NULL PRIMARY KEY  AUTO_INCREMENT,datatime timestamp  DEFAULT CURRENT_TIMESTAMP,name varchar(30)NOT NULL,category varchar(30)NOT NULL,quantity int(10) UNSIGNED NOT NULL,quantitySold int(10) UNSIGNED NOT NULL,price int(10) UNSIGNED NOT NULL,description varchar(200),img longblob NOT NULL);
        //     CREATE TABLE `inventory` (id int(10) ZEROFILL NOT NULL ,productID int(10) ZEROFILL NOT NULL ,quantity int(10) UNSIGNED NOT NULL,quantitySold int(10));
        //     CREATE TABLE `order` (orderID int(10) ZEROFILL NOT NULL ,datatime timestamp  DEFAULT CURRENT_TIMESTAMP, id int(10) ZEROFILL NOT NULL ,sellerID int(10) ZEROFILL NOT NULL,productID int(10) ZEROFILL NOT NULL ,quantity int(10) UNSIGNED NOT NULL,price int(10) UNSIGNED NOT NULL);
        //     CREATE TABLE `shopCart` (id int(10) ZEROFILL NOT NULL ,sellerID int(10) ZEROFILL NOT NULL,productID int(10) ZEROFILL NOT NULL ,quantity int(10) UNSIGNED NOT NULL,price int(10) UNSIGNED NOT NULL);
            
        //     DELIMITER $$
        //     CREATE TRIGGER ins_product
        //     AFTER INSERT ON product FOR EACH ROW BEGIN
        //     INSERT INTO inventory (id, productID, quantity)
        //     Values (new.id, new.productID, new.quantity);
        //     END;
        //     $$
        //     DELIMITER $$
        //     CREATE TRIGGER upd_product
        //     AFTER INSERT ON product FOR EACH ROW BEGIN
        //     UPDATE inventory SET quantity = new.quantity
        //     WHERE id = new.id && productID = new.productID;
        //     END;
        //     $$
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userDetail');
        Schema::dropIfExists('product');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('order');
        Schema::dropIfExists('shopCart');
    }
}
