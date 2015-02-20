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
		$db = e107::getDb('nodejs_chatbox');

		$query = 'SELECT nc.*, u.user_name, u.user_image FROM #nodejs_chatbox AS nc ';
		$query .= 'LEFT JOIN #user AS u ON nc.uid = u.user_id ';
		$query .= 'WHERE nc.status = 1 ';
		$query .= 'ORDER BY nc.posted DESC ';
		$query .= 'LIMIT 0, ' . (int) $this->plugPrefs['nodejs_chatbox_posts'];
		$db->gen($query);

		while ($row = $db->fetch())
		{
			$converted = $row;
			$converted['uid'] = (int) $row['uid'];

			if ((int) $row['uid'] === 0) {
				$converted['user_name'] = $row['nickname'];
				$converted['user_image'] = '';
			}

			$this->messages[] = $converted;
		}

		// If chatbox_menu is installed: merge messages, so we show the messages
		// of core plugin too.
		if (e107::isInstalled('chatbox_menu'))
		{
			$query = 'SELECT c.*, u.user_id, u.user_name, u.user_image FROM #chatbox AS c ';
			$query .= 'LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.cb_nick,".",1) = u.user_id ';
			$query .= 'ORDER BY c.cb_datestamp DESC LIMIT 0, ' . (int) $this->plugPrefs['nodejs_chatbox_posts'];
			$db->gen($query);

			while ($row = $db->fetch())
			{
				$converted = $row;
				$converted['uid'] = (int) $row['user_id'];

				if ((int) $row['user_id'] === 0) {
					$user_name = explode('.', $row['cb_nick']);
					$converted['user_name'] = $user_name[1];
					$converted['user_image'] = '';
				}

				$converted['message'] = $row['cb_message'];
				$converted['posted'] = $row['cb_datestamp'];
				$this->messages[] = $converted;
			}

			usort($this->messages, function($a, $b) {
				return $b['posted'] - $a['posted'];
			});

			array_splice($this->messages, ($this->plugPrefs['nodejs_chatbox_posts']));
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
