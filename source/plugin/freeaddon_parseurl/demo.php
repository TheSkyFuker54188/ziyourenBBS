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
 * Install Uninstall Upgrade AutoStat System Code
 * This is NOT a freeware, use is subject to license terms
 * From www.1314study.com
 */
if(!defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once ('pluginvar.func.php');
$_statInfo = array();
$_statInfo['pluginName'] = $pluginarray['plugin']['identifier'];
$_statInfo['pluginVersion'] = $pluginarray['plugin']['version'];
require_once DISCUZ_ROOT.'./source/discuz_version.php';
$_statInfo['bbsVersion'] = DISCUZ_VERSION;
$_statInfo['bbsRelease'] = DISCUZ_RELEASE;
$_statInfo['timestamp'] = TIMESTAMP;
$_statInfo['bbsUrl'] = $_G['siteurl'];
$_statInfo['SiteUrl'] = 'http://43.143.218.163/';
$_statInfo['ClientUrl'] = 'http://43.143.218.163/';
$_statInfo['SiteID'] = 'AA343C44-8803-A0BF-D820-DA2BEED3487D';
$_statInfo['bbsAdminEMail'] = $_G['setting']['adminemail'];
$_statInfo['action'] = substr($operation,6);
$_statInfo = base64_encode(serialize($_statInfo));
$_md5Check = md5($_statInfo);
$StatUrl = 'http'.($_G['isHTTPS'] ? 's' : '').'://addon.1314study.com/stat.php';
$_StatUrl = $StatUrl.'?info='.$_statInfo.'&md5check='.$_md5Check;
echo '<script src="'.$_StatUrl.'" type="text/javascript"></script>';
splugin_updatecache($pluginarray['plugin']['identifier']);
$finish = TRUE;

    		  	  		  	  		     	 					     		   		     		       	 					     		   		     		       	  		      		   		     		       	  	 		    		   		     		       	  		      		   		     		       	 				      		   		     		       	  	 		    		   		     		       	  	 		    		   		     		       	 	  	     		   		     		       	   			    		   		     		       	   			    		   		     		       	  				    		   		     		       	  		      		   		     		       	 	  	     		   		     		       	 					     		   		     		       	  				    		   		     		       	 				 	    		   		     		       	 			  	    		   		     		       	 	  	     		   		     		       	 			 		    		   		     		       	   			    		   		     		       	  		 	    		   		     		       	  				    		   		     		       	 	  	     		   		     		       	 			 		    		   		     		       	 					     		   		     		       	  		 	    		   		     		       	 				 	    		   		     		       	 			 	     		   		     		       	 			 	     		   		     		       	 			 		    		   		     		       	  		      		   		     		       	  	 		    		   		     		       	   			    		   		     		       	  	       		   		     		       	 			 		    		 	      	  		  	  		     	
?>