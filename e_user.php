<?php
/**
 * @file
 *
 */

if (!defined('e107_INIT'))
{
	exit;
}

class nodejs_chatbox_user
{

	function profile($udata)
	{
		if (!e107::getPlugConfig('nodejs_chatbox')->getPref('nodejs_chatbox_user_addon', true)) {
			return array();
		}

		$chatposts = 0;

		if (e107::isInstalled("chatbox_menu"))
		{
			$chatposts += e107::getDb()->count("chatbox");
		}

		if (e107::isInstalled("nodejs_chatbox"))
		{
			$chatposts += e107::getDb()->count("nodejs_chatbox");
		}

		e107::setRegistry('total_chatposts', $chatposts);

		$perc = ($chatposts > 0) ? round(($udata['user_chats'] / $chatposts) * 100, 2) : 0;


		$var = array(
			0 => array(
				'label' => LAN_PLUGIN__NODEJS_CHATBOX_POSTS,
				'text' => $udata['user_chats'] . " ( " . $perc . "% )"
			)
		);

		return $var;
	}
}