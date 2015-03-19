<?php
/**
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @file
 * Chatbox e_header Handler.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_e_header.
 */
class nodejs_chatbox_e_header
{

	private $plugPrefs = null;

	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		self::include_components();
	}

	/**
	 * Include necessary CSS and JS files.
	 *
	 * TODO: check that the nodejs_chatbox_menu is active on the site or not
	 */
	function include_components()
	{
		// If the selected handler is Ajax.
		if((int) $this->plugPrefs['ncb_handler'] === 1)
		{
			e107::js('nodejs_chatbox', 'js/nodejs_chatbox.ajax.js', 'jquery', 4);
		}

		// If emote icons is enabled.
		if($this->plugPrefs['ncb_emote'] && e107::getPref('smiley_activate', true))
		{
			e107::js('nodejs_chatbox', 'js/nodejs_chatbox.emote.js', 'jquery', 4);
		}
	}

}


// Class instantiation.
new nodejs_chatbox_e_header;
