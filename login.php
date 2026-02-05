<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 */

define('APPTYPEID', 0);
define('CURSCRIPT', 'login');

require './source/class/class_core.php';

$discuz = C::app();
$discuz->init();

$referer = !empty($_GET['referer']) ? rawurldecode($_GET['referer']) : $_G['siteurl'];
$referer = dhtmlspecialchars($referer);
$loginurl = 'member.php?mod=logging&action=login&referer='.rawurlencode($referer);
$registerurl = 'member.php?mod=register';

include template('common/login_gate');

?>
