<?php
/**
 * Node.js Chatbox plugin for e107 v2.
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

		// Only load this js, if nodejs_chatbox plugin is installed.
		if((int) $this->plugPrefs['ncb_handler'] === 2)
		{
			e107::js('nodejs_chatbox', 'js/nodejs_chatbox.nodejs.js', 'jquery', 5);
		}
	}

}


// Class instantiation.
new nodejs_chatbox_e_header;
