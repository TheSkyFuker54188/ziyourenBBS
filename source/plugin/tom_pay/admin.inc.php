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

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$LangTmp = $scriptlang['tom_pay'];
$adminBaseUrl = ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=tom_pay&pmod=admin'; 
$adminListUrl = 'action=plugins&operation=config&do='.$pluginid.'&identifier=tom_pay&pmod=admin';
$adminFromUrl = 'plugins&operation=config&do=' . $pluginid . '&identifier=tom_pay&pmod=admin';
$tomSysOffset = getglobal('setting/timeoffset');

$nowDayTime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$tomSysOffset),dgmdate($_G['timestamp'], 'j',$tomSysOffset),dgmdate($_G['timestamp'], 'Y',$tomSysOffset)) - $tomSysOffset*3600;
$nowMonthTime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$tomSysOffset),1,dgmdate($_G['timestamp'], 'Y',$tomSysOffset)) - $tomSysOffset*3600;

$pluginVarList = C::t('common_pluginvar')->fetch_all_by_pluginid($pluginid);
$payConfig = array();
foreach ($pluginVarList as $vark => $varv){
    $payConfig[$varv['variable']] = $varv['value'];
}

$Lang  = array();
if(is_array($LangTmp) && !empty($LangTmp)){
    foreach ($LangTmp as $key => $value){
        $Lang[$key] = htmlspecialchars_decode($value);
    }
}

if($_GET['tmod'] == 'order'){
    
    include DISCUZ_ROOT.'./source/plugin/tom_pay/admin/order.php';
    
}else if($_GET['tmod'] == 'addon'){
    
    include DISCUZ_ROOT.'./source/plugin/tom_pay/admin/addon.php';
    
}else{
    
    include DISCUZ_ROOT.'./source/plugin/tom_pay/admin/order.php';
    
}