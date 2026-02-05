<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: [点微]支付中心 6.3
 *      下载地址: https://addon.dismall.com/plugins/tom_pay.html
 *      应用开发者: 点微科技
 *      开发者QQ: 800046233
 *      更新日期: 202602051144
 *      授权域名: 43.143.218.163
 *      授权码: 2026020503VZ4u4wh6Fb
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */


/*
   This is NOT a freeware, use is subject to license terms
   版权所有：点微科技 www.tomwx.cn
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/plugin');

if(isset($_G['uid']) && $_G['uid'] > 0 && $_G['groupid'] == 1){
    
    $sql = '';
    
    $tom_pay_order_field = C::t('#tom_pay#tom_pay_order')->fetch_all_field();
    if (!isset($tom_pay_order_field['qf_order_id'])) {
        $sql .= "ALTER TABLE `pre_tom_pay_order` ADD `qf_order_id` int(11) DEFAULT '0';\n";
    }
    if (!isset($tom_pay_order_field['mag_order_id'])) {
        $sql .= "ALTER TABLE `pre_tom_pay_order` ADD `mag_order_id` varchar(255) DEFAULT NULL;\n";
    }
    if (!isset($tom_pay_order_field['user_id'])) {
        $sql .= "ALTER TABLE `pre_tom_pay_order` ADD `user_id` int(11) DEFAULT '0';\n";
    }
    
    if (!empty($sql)) {
        runquery($sql);
    }

    echo 'OK';exit;
    
}else{
    exit('Access Denied');
}