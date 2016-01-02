<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Render a page to list all chatbox messages and provide the ability to moderate.
 */

if(!defined('e107_INIT'))
{
	require_once('../../class2.php');
}

if(!e107::isInstalled('nodejs_chatbox'))
{
	header('Location: ' . e_BASE . 'index.php');
	exit;
}

// [PLUGINS]/nodejs_chatbox/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('nodejs_chatbox', false, true);

require_once(HEADERF);


/**
 * Class nodejs_chatbox.
 */
class nodejs_chatbox
{

	/**
	 * Store plugin preferences.
	 *
	 * @var mixed|null
	 */
	private $plugPrefs = null;

	/**
	 * Moderator permission for current, logged in user.
	 *
	 * @var bool
	 */
	private $moderate = false;

	/**
	 * Store chatbox messages.
	 *
	 * @var array
	 */
	private $messages = array();

	private $chatTotal = 0;

	private $chatFrom = 0;


	/**
	 * Constructor.
	 */
	function __construct()
	{
		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		if ($this->checkMenuClass()) {
			// If no moderate class selected, we set a default value.
			if(!isset($this->plugPrefs['ncb_mod']))
			{
				$this->plugPrefs['ncb_mod'] = e_UC_ADMIN;
			}
			$this->moderate = check_class($this->plugPrefs['ncb_mod']);

			if(isset($_POST['moderate']) && $_POST['moderate'] && $this->moderate)
			{
				$this->moderateMessages();
			}

			e_QUERY ? $this->chatFrom = intval(e_QUERY) : $this->chatFrom = 0;
			$db = e107::getDb('nodejs_chatbox');
			$this->chatTotal = $db->count('nodejs_chatbox');

			$this->getMessages();
			$this->renderPage();
		}
	}


	/**
	 * Select messages from database.
	 */
	function getMessages()
	{
		$db = e107::getDb('nodejs_chatbox');

		// When coming from search.php.
		if(strstr(e_QUERY, "fs"))
		{
			$cgtm = intval(str_replace(".fs", "", e_QUERY));
			$fs = true;
		}

		$qry_where = ($this->moderate ? "1" : "ncb_blocked=0");

		// When coming from search.php calculate page number.
		if(isset($fs) && $fs)
		{
			$page_count = 0;
			$row_count = 0;
			$db->select("nodejs_chatbox", "*", "{$qry_where} ORDER BY ncb_datestamp DESC");

			while($row = $db->fetch())
			{
				if(isset($cgtm) && $row['ncb_id'] == $cgtm)
				{
					$this->chatFrom = $page_count;
					break;
				}
				$row_count++;
				if($row_count == 30)
				{
					$row_count = 0;
					$page_count += 30;
				}
			}
		}

		$query = 'SELECT c.*, u.user_id, u.user_name, u.user_login, u.user_image FROM #nodejs_chatbox AS c ';
		$query .= 'LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,".",1) = u.user_id ';
		$query .= 'WHERE ' . $qry_where . ' ';
		$query .= 'ORDER BY c.ncb_datestamp DESC ';
		$query .= 'LIMIT ' . intval($this->chatFrom) . ', 30';
		$db->gen($query);

		while($row = $db->fetch())
		{
			$message = $row;
			// If the author is not a registered user, we have to get the nickname.
			if((int) $row['user_id'] === 0)
			{
				list($cb_uid, $cb_nick) = explode(".", $row['ncb_nick'], 2);
				$message['user_name'] = $cb_nick;
				$message['user_image'] = '';
			}

			$this->messages[] = $message;
		}
	}


	function renderPage()
	{
		$mes = e107::getMessage();
		$template = e107::getTemplate('nodejs_chatbox');
		$sc = e107::getScBatch('nodejs_chatbox', true);
		$tp = e107::getParser();
		$frm = e107::getForm();

		$text = $tp->parseTemplate($template['moderate_start'], true, $sc);
		foreach($this->messages as $row)
		{
			$sc->setVars($row);
			$text .= $tp->parseTemplate($template['moderate_item'], true, $sc);

			if($this->moderate)
			{
				$text .= '<div class="checkbox">';
				$text .= '<label>';
				$text .= $frm->checkbox('delete[' . $row['ncb_id'] . ']', 1, false, array(
						'id' => 'delete-' . $row['ncb_id'],
				));
				$text .= ' ' . LAN_DELETE . '</label>';
				$text .= '</div>';

				if($row['cb_blocked'])
				{
					$text .= '<div class="checkbox">';
					$text .= '<label>';
					$text .= $frm->checkbox('unblock[' . $row['ncb_id'] . ']', 1, false, array(
							'id' => 'unblock-' . $row['ncb_id'],
					));
					$text .= ' ' . LAN_NCB_FRONT_21 . '</label>';
					$text .= '</div>';
				}
				else
				{
					$text .= '<div class="checkbox">';
					$text .= '<label>';
					$text .= $frm->checkbox('block[' . $row['ncb_id'] . ']', 1, false, array(
							'id' => 'block-' . $row['ncb_id'],
					));
					$text .= ' ' . LAN_NCB_FRONT_22 . '</label>';
					$text .= '</div>';
				}
			}
		}
		$text .= $tp->parseTemplate($template['moderate_end'], true, $sc);

		if($this->moderate)
		{
			$form = $frm->open('moderate', 'post', e_SELF, array(
					'class' => 'form-inline',
			));
			$form .= $text;
			$form .= $frm->button('moderate', 1, 'submit', LAN_NCB_FRONT_24);
			$form .= $frm->close();

			$text = $form;
		}

		$parms = "{$this->chatTotal},30,{$this->chatFrom}," . e_SELF . '?[FROM]';
		$text .= "<div class='nextprev'>" . $tp->parseTemplate("{NEXTPREV={$parms}}") . '</div>';

		e107::getRender()->tablerender(LAN_NCB_FRONT_19, $mes->render() . $text);
		unset($text);
	}


	function checkMenuClass()
	{
		$db = e107::getDb();

		$db->select("menus", "*", "menu_name='nodejs_chatbox_menu'");
		$row = $db->fetch();

		if(!check_class(vartrue($row['menu_class'], 0)))
		{
			$mes = e107::getMessage();
			$mes->addError(LAN_NCB_FRONT_15);

			e107::getRender()->tablerender(LAN_ERROR, $mes->render());
			unset($text);

			return false;
		}

		return true;
	}


	function moderateMessages()
	{
		if(isset($_POST['block']) && is_array($_POST['block']))
		{
			$kk = array();
			foreach(array_keys($_POST['block']) as $k)
			{
				$kk[] = intval($k);
			}
			$blocklist = implode(",", $kk);

			$db = e107::getDb();
			$db->gen("UPDATE #nodejs_chatbox SET ncb_blocked=1 WHERE ncb_id IN ({$blocklist})");
		}

		if(isset($_POST['unblock']) && is_array($_POST['unblock']))
		{
			$kk = array();
			foreach(array_keys($_POST['unblock']) as $k)
			{
				$kk[] = intval($k);
			}
			$unblocklist = implode(",", $kk);

			$db = e107::getDb();
			$db->gen("UPDATE #nodejs_chatbox SET ncb_blocked=0 WHERE ncb_id IN ({$unblocklist})");
		}

		if(isset($_POST['delete']) && is_array($_POST['delete']))
		{
			$db = e107::getDb();

			$deletelist = implode(",", array_keys($_POST['delete']));

			$db->gen("SELECT c.ncb_id, u.user_id FROM #nodejs_chatbox AS c
					LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,'.',1) = u.user_id
					WHERE c.ncb_id IN (" . $deletelist . ")");

			$rowlist = $db->db_getList();
			foreach($rowlist as $row)
			{
				$db->gen("UPDATE #user SET user_chats=user_chats-1 where user_id = " . intval($row['user_id']));
			}
			$db->gen("DELETE FROM #nodejs_chatbox WHERE ncb_id IN ({$deletelist})");
		}

		e107::getCache()->clear("nodejs_chatbox");

		$mes = e107::getMessage();
		$mes->addSuccess(LAN_NCB_FRONT_16);
	}
}


new nodejs_chatbox();

require_once(FOOTERF);
exit;
