<?php

/**
 *      This is NOT a freeware, use is subject to license terms
 *      应用名称: 显示发帖Ip归属地 商业版
 *      下载地址: https://addon.dismall.com/plugins/twpx_iphome.html
 *      应用开发者: 天外飘仙
 *      开发者QQ: 860855665
 *      更新日期: 202602051147
 *      授权域名: 43.143.218.163
 *      授权码: 2026020503uar4Nz9Mej
 *      未经应用程序开发者/所有者的书面许可，不得进行反向工程、反向汇编、反向编译等，不得擅自复制、修改、链接、转载、汇编、发表、出版、发展与之有关的衍生产品、作品等
 */

 /**
 * 作者: 天外飘仙
 * 业务范围：插件定制,discuz技术支持,服务器技术支持，云服务器销售————价格优惠不玩套路
 * 应用中心：https://addon.dismall.com/developer-99821.html
 * 我的网站：https://bbs.piaoxian.net
 * 
 * 联系QQ 860855665
 * 联系电话/微信：   158 388 31583
 * 用户交流qq群: 1群(73131419) 2群(112537934) 3群(131429791)
 * 工作时间: 周一到周五早上09:00-11:50, 下午02:00-05:30, 晚上08:00-10:00 (周末休息)
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class _twpx_iphome {
	public $config,$groups,$opengroups,$forums,$groupforums,$refusearea,$prefix,$ipdata,$token,$filter;
	function __construct() {
		global $_G;
		if(empty($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		$this->config = $_G['cache']['plugin']['twpx_iphome'];
		$this->groups = unserialize($this->config['groups']);
		$this->opengroups = unserialize($this->config['opengroups']);
		$this->forums = unserialize($this->config['forums']);
		$groupforums = explode(',',$this->config['groupforums']);
		$this->groupforums = $groupforums;
		$this->forums = array_merge($this->forums,$this->groupforums);
		$this->refusearea = explode('|',$this->config['refusearea']);
		$this->prefix = $this->config['prefix'];
		$this->ipdata = $this->config['ipdata'];
		$this->token = $this->config['token'];
		$this->filter = $this->config['filter'];
	}

	function _iphome() {
		global $_G,$postlist;
		$output = array();

		$i = 0;
		foreach ($postlist as $post){
			if(!in_array($post['groupid'],$this->opengroups)) {
				$i++;
				continue;
			}

			$area = '';
			$userip = $post['useip'];
			$ip138func = DISCUZ_ROOT.'/source/plugin/twpx_iphome/lib/function.php';
			if($this->ipdata == 2 && file_exists($ip138func)) {
				// 加载缓存
				loadcache('twpx_iphome');
				$cache = $_G['cache']['twpx_iphome'];
				$area = $cache[$userip]; // 先查缓存
				//缓存无对应数据,再查 数据库
				if (empty($area)) {
					# code...
					$area = DB::result_first('SELECT area FROM %t WHERE ip=%s',array('plugin_twpx_iphome',$userip));
					//查完数据库 立即写入缓存
					if ($cache == null && !empty($area)) {
						$cache = array();
						$data = $cache + array($userip=>$area);
						savecache('twpx_iphome',$data);
					}
				}
				//写入缓存处理结束
				// 缓存 数据库均无数据,再查 ip138
				if(empty($area)) {
					include_once($ip138func);
					$header = array('token:'.$this->config['token']);
					$area = getIpData($userip,$header);
				}

				//去除营运商信息
				if(!$this->config['clearserviceprovider'] && !empty($area)) {
					$pos = strrpos($area,' ');
					$area = $pos!==false ? substr($area,0,$pos):$area;
				}

			}
			// 如选dz自带ip库 或者 其他查询失败 则 使用dz自带ip库查询
			if($this->ipdata == 1 || empty($area)) {
				require_once libfile('function/misc');
				$area = ltrim(convertip($userip),'-');
				$n = preg_match_all('/ /',$area);
				//去除运营商信息
				if($n>1 && $this->config['clearserviceprovider']){
					$pos = strrpos($area,' ');
					$area = $pos!==false ? substr($area,0,$pos):$area;
				}
			}
			//附加样式
			$style = "style={$this->config['style']}";
			// debug($this->config['style']);
			$area = "<span {$style}>{$area}</span>";
			$output[$i] = $this->prefix.$area;
			$i++;
		}
		//内容过滤
		$filter = explode('|',$this->config['filter']);
		$output = str_replace($filter,'',$output);
		return $output;
	}

	function _refusepost() {	
		if(empty($this->refusearea[0])) {
			return '';
		}	
		global $_G;
		$ip = $_G['clientip'];
		require_once libfile('function/misc');
		$area = convertip($ip);
		foreach( $this->refusearea as $v ) {
			$str = strstr($area,$v);
			if($str) {
				showmessage(lang('plugin/twpx_iphome','notice'));
				break;
			}
		}
	}
	function _outputjs($output) {
		// $i = 0; // 这里的 $i 可以直接用 $key 代替
		foreach ($output as $key => $v) {
			// $j = "document.getElementsByClassName('grey rela')";
			$j = $this->config['mplace']."[{$key}]";
			// $output[$key] = "<script> {$j}.append('{$v}')</script>";
			$output[$key] = "<script> {$j}.innerHTML +='{$v}'</script>";
			// $i++;
		}
		return $output;
	}
}

class plugin_twpx_iphome extends _twpx_iphome {}
class plugin_twpx_iphome_forum extends plugin_twpx_iphome {
	//禁止指定地区发帖
	function post_top() {
		$this->_refusepost();
		return '';
	}

	//显示发帖ip归属地
	function viewthread_postheader_output() {
		global $_G,$postlist;
		if( in_array($_G['groupid'],$this->groups) && in_array($_G['fid'],$this->forums) ) {
			return $this->_iphome();
		}
		return array();

	}
}
class plugin_twpx_iphome_group extends plugin_twpx_iphome_forum {
	function viewthread_postheader_output() {
		global $_G;
		if(in_array($_G['groupid'],$this->groups) && (in_array('0',$this->groupforums)||in_array($_G['fid'],$this->groupforums)) ) {
			return $this->_iphome();
		}
		return array();
	}
}

class mobileplugin_twpx_iphome extends _twpx_iphome {}
class mobileplugin_twpx_iphome_forum extends mobileplugin_twpx_iphome {
	function post_middle_mobile() {
		$this->_refusepost();
		return '';
	}
	function viewthread_postbottom_mobile_output() {
		global $_G;
		if(!empty($this->config['mplace'])){
			$output = array();
			if( in_array($_G['groupid'],$this->groups) && in_array($_G['fid'],$this->forums) ) {
				$output = $this->_iphome();
			}
			return $this->_outputjs($output);
		}
		return array();
	}
	function viewthread_posttop_mobile_output() {
		global $_G,$postlist;
		if(empty($this->config['mplace'])){
			$output = $this->_iphome();
			if( in_array($_G['groupid'],$this->groups) && in_array($_G['fid'],$this->forums) ) {
				$i = 0;
				foreach($postlist as &$post) {
					if(!in_array($post['groupid'],$this->opengroups)) {
						$i++;
						continue;
					}
					$post['dateline'] .= $output[$i];
					$i++;

				}
			}
		}
		return array();
	}
}
class mobileplugin_twpx_iphome_group extends mobileplugin_twpx_iphome_forum{
	function post_middle_mobile() {
		$this->_refusepost();
		return '';
	}
	function viewthread_postbottom_mobile_output() {
		global $_G;
		if(!empty($this->config['mplace'])){
			$output = array();
			if( in_array($_G['groupid'],$this->groups) && (in_array('0',$this->groupforums)||in_array($_G['fid'],$this->groupforums)) ) {
				$output = $this->_iphome();
			}
			return $this->_outputjs($output);
		}
		return array();
	}
	function viewthread_posttop_mobile_output() {
		global $_G,$postlist;
		if(empty($this->config['mplace'])){
			$output = $this->_iphome();
			if( in_array($_G['groupid'],$this->groups) && (in_array('0',$this->groupforums)||in_array($_G['fid'],$this->groupforums)) ) {
				$i = 0;
				foreach($postlist as &$post) {
					if(!in_array($post['groupid'],$this->opengroups)) {
						$i++;
						continue;
					}
					$post['dateline'] .= $output[$i];
					$i++;

				}
			}
		}
		return array();
	}

}

    		  	  		  	  		     	  	 			    		   		     		       	   	 		    		   		     		       	   	 		    		   		     		       	   				    		   		     		       	   	 	    		   		     		       	 	        		   		     		       	 	        		   		     		       	  	 		    		   		     		       	  		      		   		     		       	 	   	    		   		     		       	  			     		   		     		       	  	 		    		   		     		       	  		      		   		     		       	 	   	    		   		     		       	  		 	    		   		     		       	  			     		   		     		       	   			    		   		     		       	 	   	    		   		     		       	  			     		   		     		       	  	  	    		   		     		       	  		      		   		     		       	 	        		 	      	  		  	  		     	
?>