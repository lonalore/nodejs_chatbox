<?php
/**
 * e107 website system
 *
 * Copyright (C) 2008-2015 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @file
 * Chatbox plugin - Dashboard (Status).
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_dashboard.
 */
class nodejs_chatbox_dashboard // include plugin-folder in the name.
{

	function chart()
	{
		return false;
	}


	function status()
	{
		$sql = e107::getDb();
		$chatbox_posts = $sql->count('nodejs_chatbox');

		$src = e_PLUGIN_ABS . "nodejs_chatbox/images/chatbox_16.png";

		$var[0]['icon'] = "<img src='" . $src . "' style='width: 16px; height: 16px; vertical-align: bottom' alt='' /> ";
		$var[0]['title'] = LAN_PLUGIN_NODEJS_CHATBOX_POSTS;
		$var[0]['url'] = e_PLUGIN . "nodejs_chatbox/admin_config.php";
		$var[0]['total'] = $chatbox_posts;

		return $var;
	}
}
