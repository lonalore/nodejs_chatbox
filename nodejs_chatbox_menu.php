<?php
/**
 * @file
 * Class to render an e107 menu for plugin.
 */

if (!defined('e107_INIT'))
{
	exit();
}

if (!plugInstalled('nodejs_chatbox'))
{
	exit;
}

e107::lan('nodejs_chatbox', false, true);

/**
 * Class nodejs_chatbox.
 */
class nodejs_chatbox_menu
{

	private $plugPrefs = null;

	private $moderate = false;

	private $messages = array();


	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		if (!isset($this->plugPrefs['nodejs_chatbox_mod']))
		{
			$this->plugPrefs['nodejs_chatbox_mod'] = e_UC_ADMIN;
		}

		$this->moderate = check_class($this->plugPrefs['nodejs_chatbox_mod']);
		$this->getMessages();
		$this->renderMenu();
	}


	function getMessages()
	{
		$query = 'SELECT nc.*, u.user_name, u.user_image FROM #nodejs_chatbox AS nc ';
		$query .= 'LEFT JOIN #user AS u ON nc.uid = u.user_id ';
		$query .= 'WHERE nc.status = 1 ';
		$query .= 'ORDER BY nc.posted DESC ';
		$query .= 'LIMIT ' . (int) $this->plugPrefs['nodejs_chatbox_posts'];

		$db = e107::getDb('nodejs_chatbox');
		$db->gen($query);

		while ($row = $db->fetch())
		{
			$this->messages[] = $row;
		}
	}


	function renderMenu()
	{
		$template = e107::getTemplate('nodejs_chatbox');
		$sc = e107::getScBatch('nodejs_chatbox', true);
		$tp = e107::getParser();

		$text = $tp->parseTemplate($template['HEADER'], true, $sc);
		foreach ($this->messages as $val)
		{
			$sc->setVars($val);
			$text .= $tp->parseTemplate($template['BODY'], true, $sc);
		}
		$sc->setVars($this->moderate);
		$text .= $tp->parseTemplate($template['FOOTER'], true, $sc);
		$text = '<div class="nodejs-chatbox-wrapper">' . $text . '</div>';

		e107::getRender()->tablerender(LAN_NODEJS_CHATBOX_01, $text);
		unset($text);
	}
}

new nodejs_chatbox_menu();
