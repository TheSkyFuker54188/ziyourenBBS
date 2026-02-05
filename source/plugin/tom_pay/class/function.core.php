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

function checkDirNameChar($str = ''){
    $flag = false;
    if ($str && preg_match("#^[a-zA-Z0-9_]+$#", $str)){
        $flag = true;
    }
    return  $flag;				
}

function iconv_to_utf8($value){
    if(is_array($value)) {
        foreach($value AS $key => $val) {
            $value[$key] = iconv_to_utf8($val);
        }
    } else {
        $value = diconv($value,CHARSET,'utf-8');
    }
    return $value;
}

function iconv_utf8_to_gbk($value){
    if(is_array($value)) {
        foreach($value AS $key => $val) {
            $value[$key] = iconv_utf8_to_gbk($val);
        }
    } else {
        $value = diconv($value,'utf-8',CHARSET);
    }
    return $value;
}

function getHtml($url){
    if(function_exists('curl_init')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $return = curl_exec($ch);
        curl_close($ch); 
        return $return;
    }
    return false;
}

function payFilterEmojiMatches($match){
	return strlen($match[0]) >= 4 ? '' : $match[0];
}

function payFilterEmoji($str){
    
    if(CHARSET == 'utf-8') {
        $str = preg_replace_callback(
           '/./u',
           'payFilterEmojiMatches',
		   $str);
    }

    return $str;
  
}