<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: 手机发链接自动解析 免费版
 *      下载地址: https://addon.dismall.com/plugins/freeaddon_parseurl.html
 *      应用开发者: FreeAddon
 *      开发者QQ: 15326940
 *      更新日期: 202602051147
 *      授权域名: 43.143.218.163
 *      授权码: 2026020503EsqYX4w9RY
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */

/*
 * Install Uninstall Upgrade AutoStat System Code 2026020503EsqYX4w9RY
 * This is NOT a freeware, use is subject to license terms
 * From www.1314study.com
 */
if(!defined('IN_ADMINCP')) {
	exit('Access Denied');
}

empty($pluginarray['plugin']) && $pluginarray['plugin'] = $plugin;

require_once DISCUZ_ROOT.'./source/discuz_version.php';
require_once DISCUZ_ROOT.'./source/plugin/'.$pluginarray['plugin']['identifier'].'/installlang.lang.php';
$request_url = str_replace('&step='.$_GET['step'],'',$_SERVER['QUERY_STRING']);

//3.1以后版本直接跳到删除数据库
if(str_replace('X', '', DISCUZ_VERSION) >= 3.1){
	$_GET['step'] = 'sql';
	$_GET['deletesql'] = '2026020503EsqYX4w9RY';
}
$identifier = $identifier ? $identifier : $pluginarray['plugin']['identifier'];

$available = dfsockopen('http://addon.1314study.com/api/available.php?siteurl='.rawurlencode($_G['siteurl']).'&identifier='.$identifier, 0, '', '', false, '', 5);
if($available == 'succeed'){
	$available = 1;
}else{
	$available = 0;
}

//$sql = <<<EOF
//DROP TABLE IF EXISTS cdb_study_demo;
//EOF;
//runquery($sql);

$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
$_statInfo['bbsVersion'] = DISCUZ_VERSION;
$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
$_statInfo['timestamp'] = TIMESTAMP;
$_statInfo['bbsUrl'] = $_G['siteurl'];
$_statInfo['SiteUrl'] = 'http://43.143.218.163/';
$_statInfo['ClientUrl'] = 'http://43.143.218.163/';
$_statInfo['SiteID'] = 'AA343C44-8803-A0BF-D820-DA2BEED3487D';
$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
$_statInfo['action'] = 'uninstall';
$_statInfo['genuine'] = splugin_genuine($pluginarray['plugin']['identifier']);
$_statInfo = base64_encode(serialize($_statInfo));
$_md5Check = md5($_statInfo);
$StatUrl = 'http://addon.1314study.com/stat.php';
$_StatUrl = $StatUrl.'?info='.$_statInfo.'&md5check='.$_md5Check;
dfsockopen($_StatUrl, 0, '', '', false, '', 5);
$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['bbsVersion'] = DISCUZ_VERSION;
$_statInfo['bbsUrl'] = $_G['siteurl'];
$_statInfo['action'] = 'uninstall';
$_statInfo['nextUrl'] = ADMINSCRIPT.'?'.$request_url;
$_statInfo = base64_encode(serialize($_statInfo));
$_md5Check = md5($_statInfo);
$_StatUrl = 'http'.($_G['isHTTPS'] ? 's' : '').'://addon.1314study.com/api/outer_addon.php?type=js&info='.$_statInfo.'&md5check='.$_md5Check;
if(preg_match("/^[a-z]+[a-z0-9_]*$/i", $identifier)){
	if(function_exists('cron_delete')) {
		cron_delete($identifier);
	}
	loadcache('pluginlanguage_install', 1);
	if(!empty($_G['cache']['pluginlanguage_install']) && isset($_G['cache']['pluginlanguage_install'][$identifier])) {
		unset($_G['cache']['pluginlanguage_install'][$identifier]);
		savecache('pluginlanguage_install', $_G['cache']['pluginlanguage_install']);
	}
	cloudaddons_uninstall($identifier.'.plugin', DISCUZ_ROOT.'./source/plugin/'.$identifier);
}
C::t('common_syscache')->delete('scache_'.$pluginarray['plugin']['identifier']);

cpmsg('plugins_delete_succeed', $_StatUrl, 'succeed');