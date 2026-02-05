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

session_start();
define('TPL_DEFAULT', true);
$formhash = FORMHASH;
$payConfig = $_G['cache']['plugin']['tom_pay'];
$tomSysOffset = getglobal('setting/timeoffset');
$nowDayTime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$tomSysOffset),dgmdate($_G['timestamp'], 'j',$tomSysOffset),dgmdate($_G['timestamp'], 'Y',$tomSysOffset)) - $tomSysOffset*3600;
require_once libfile('function/discuzcode');
$appid = trim($payConfig['wxpay_appid']);  
$appsecret = trim($payConfig['wxpay_appsecret']);
$cssJsVersion = "20251219";
$prand = rand(1, 1000);

include DISCUZ_ROOT.'./source/plugin/tom_pay/class/weixin.class.php';
$weixinClass = new weixinClass($appid,$appsecret);

$_isWeiXin = $__IsQianfan = $__IsXiaoyun = $__IsMagapp = $__IsMocuzapp = $__IsMiniprogram = $__Ios = $__Android = 0;
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){ $_isWeiXin = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'QianFan') !== false){ $__IsQianfan = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Appbyme') !== false){ $__IsXiaoyun = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MAGAPP') !== false){ $__IsMagapp = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MocuzApp') !== false){ $__IsMocuzapp = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false){$__Ios = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false){$__Android = 1;}
if(strpos($_SERVER['HTTP_USER_AGENT'], 'OpenHarmony') !== false){$__Android = 1;}
$cookie_tom_miniprogram = getcookie('tom_miniprogram');
if($cookie_tom_miniprogram == 1 || $_GET['f'] == 'miniprogram'){ $__IsMiniprogram = 1;}

if($_isWeiXin == 1){
    include DISCUZ_ROOT.'./source/plugin/tom_pay/oauth2.php';
}

$__UserInfo = array();
$__MemberInfo = array();
if(file_exists(DISCUZ_ROOT.'./source/plugin/tom_tongcheng/tom_tongcheng.inc.php')){
    $cookieUid = getcookie('tom_ucenter_member_uid');
    $cookieKey = getcookie('tom_ucenter_member_key');
    if(!empty($cookieUid) && !empty($cookieKey)){
        $__MemberInfoTmp = C::t('#tom_ucenter#tom_ucenter_member')->fetch_by_uid($cookieUid);
        if($__MemberInfoTmp && !empty($__MemberInfoTmp['mykey'])){
            if(md5($__MemberInfoTmp['uid'].'|||'.$__MemberInfoTmp['mykey']) == $cookieKey){
                $__MemberInfo = $__MemberInfoTmp;
                $userInfoTmp = C::t('#tom_tongcheng#tom_tongcheng_user')->fetch_by_member_id($__MemberInfo['uid']);
                if($userInfoTmp){
                    $__UserInfo = $userInfoTmp;
                }
            }
        }
    }
}

if($_GET['mod'] == 'wap'){
    
    include DISCUZ_ROOT.'./source/plugin/tom_pay/module/wap.php';
    
}else{
    
    include DISCUZ_ROOT.'./source/plugin/tom_pay/module/wap.php';
}