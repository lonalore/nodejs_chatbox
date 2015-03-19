<?php
/**
 * e107 website system
 *
 * Copyright (C) 2008-2014 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @file
 * Chatbox plugin - User profile addon.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_user.
 */
class nodejs_chatbox_user // plugin-folder + '_user'
{

	function profile($udata)
	{
		$plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		if(!$plugPrefs['ncb_user_addon'])
		{
			return array();
		}

		if(!$chatposts = e107::getRegistry('total_chatposts'))
		{
			$chatposts = 0; // In case plugin not installed
			if(e107::isInstalled("nodejs_chatbox"))
			{
				$chatposts = e107::getDb()->count("nodejs_chatbox");
			}
			e107::setRegistry('total_chatposts', $chatposts);
		}

		$perc = ($chatposts > 0) ? round(($udata['user_chats'] / $chatposts) * 100, 2) : 0;

		$var = array(
			0 => array(
				'label' => LAN_PLUGIN_NODEJS_CHATBOX_POSTS,
				'text'  => $udata['user_chats'] . " ( " . $perc . "% )"
			)
		);

		return $var;
	}

}