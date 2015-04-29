<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Addon file to integrate Node.js with "nodejs" plugin.
 */


/**
 * Class nodejs_chatbox_nodejs.
 */
class nodejs_chatbox_nodejs
{

	/**
	 * Node.js Javascript handlers.
	 *
	 * @return array
	 *    The list of JavaScript handler files.
	 */
	public function jsHandlers()
	{
		$plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		// Only load this js, if nodejs_chatbox plugin is installed.
		// TODO: check that the nodejs_chatbox_menu is active on the site or not
		if((int) $plugPrefs['ncb_handler'] === 2)
		{
			return array(
				'js/nodejs_chatbox.nodejs.js',
			);
		}

		return array();
	}


	/**
	 * Node.js message handlers.
	 *
	 * @return array
	 *    The list of message callbacks.
	 */
	public function msgHandlers()
	{
		return array();
	}


	/**
	 * Node.js user channels.
	 *
	 * @return array
	 *    The list of user channels.
	 */
	public function userChannels()
	{
		return array();
	}


	/**
	 * Node.js user presence list.
	 *
	 * @param $account
	 *
	 * @return array
	 *    List of users who can see presence notifications about me.
	 */
	public function userPresenceList($account)
	{
		return array();
	}

}
