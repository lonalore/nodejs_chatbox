<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Chatbox plugin - Status
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_status.
 */
class nodejs_chatbox_status // include plugin-folder in the name.
{

	function config()
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
