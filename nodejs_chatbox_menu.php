<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Class to render chatbox menu, and handle post requests.
 */

// If the request is an Ajax request.
if(isset($_POST['ncb_sent_ajax']))
{
	if(!defined('e107_INIT'))
	{
		require_once('../../class2.php');
	}
}
else
{
	if(!defined('e107_INIT'))
	{
		exit;
	}
}

// [PLUGINS]/nodejs_chatbox/languages/[LANGUAGE]/[LANGUAGE]_front.php
e107::lan('nodejs_chatbox', false, true);


/**
 * Class nodejs_chatbox_menu.
 */
class nodejs_chatbox_menu
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


	/**
	 * Constructor.
	 */
	function __construct()
	{
		// Get plugin preferences.
		$this->plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

		// If no moderate class selected, we set a default value.
		if(!isset($this->plugPrefs['ncb_mod']))
		{
			$this->plugPrefs['ncb_mod'] = e_UC_ADMIN;
		}

		$this->moderate = check_class($this->plugPrefs['ncb_mod']);

		// If the form is submitted.
		if(isset($_POST['ncb_sent']) && (int) $_POST['ncb_sent'] === 1)
		{
			// Need to validate it.
			$result = $this->validateForm();

			// If submitted values are not valid.
			if(isset($result['status']) && $result['status'] == 'error')
			{
				// And the request is an Ajax request.
				if(e_AJAX_REQUEST || isset($_POST['ncb_sent_ajax']))
				{
					// We echos a JSON object contains details of error.
					echo json_encode($result);
					exit;
				}
			}
			else
			{
				// Try to save message into database.
				$this->saveMessage();
			}
		}

		// If the request is not an Ajax request, we render the full chatbox menu.
		if(!e_AJAX_REQUEST && !isset($_POST['ncb_sent_ajax']))
		{
			$this->renderMenu();
		}
	}


	/**
	 * Render chatbox menu.
	 */
	function renderMenu()
	{
		$cache = e107::getConfig();

		if(!$text = $cache->get("nodejs_chatbox"))
		{
			$this->getMessages();

			$template = e107::getTemplate('nodejs_chatbox');
			$sc = e107::getScBatch('nodejs_chatbox', true);
			$tp = e107::getParser();

			$text = $tp->parseTemplate($template['menu_start'], true, $sc);

			foreach($this->messages as $val)
			{
				$sc->setVars($val);
				$text .= $tp->parseTemplate($template['menu_item'], true, $sc);
			}

			$vars = array(
				'moderate' => $this->moderate,
			);

			$sc->setVars($vars);
			$text .= $tp->parseTemplate($template['menu_end'], true, $sc);

			$text = '<div class="chatbox-wrapper" id="cb-wrapper">' . $text . '</div>';

			$cache->set("nodejs_chatbox", $text);
		}

		e107::getRender()->tablerender(LAN_NCB_FRONT_01, $text);
		unset($text);
	}


	/**
	 * Select messages from database.
	 */
	function getMessages()
	{
		$db = e107::getDb('nodejs_chatbox');

		$query = 'SELECT c.*, u.user_id, u.user_name, u.user_image FROM #nodejs_chatbox AS c ';
		$query .= 'LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,".",1) = u.user_id ';
		$query .= 'ORDER BY c.ncb_datestamp DESC ';
		$query .= 'LIMIT 0, ' . (int) $this->plugPrefs['ncb_posts'];
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


	/**
	 * Render HTML output of a chatbox message. Used for Ajax response.
	 *
	 * @param int $cb_id
	 *  Unique ID of message in database table.
	 *
	 * @return string $html
	 *  Rendered HTML output.
	 */
	function getMessage($cb_id)
	{
		$db = e107::getDb('nodejs_chatbox');
		$template = e107::getTemplate('nodejs_chatbox');
		$sc = e107::getScBatch('nodejs_chatbox', true);
		$tp = e107::getParser();

		$query = 'SELECT c.*, u.user_id, u.user_name, u.user_image FROM #nodejs_chatbox AS c ';
		$query .= 'LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,".",1) = u.user_id ';
		$query .= 'WHERE c.ncb_id=' . (int) $cb_id;
		$db->gen($query);

		$message = array();
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
		}

		$sc->setVars($message);
		$html = $tp->parseTemplate($template['menu_item'], true, $sc);

		return $html;
	}


	/**
	 * Validate form submits.
	 *
	 * @return array $message
	 *  Empty array if the submitted values are valid, or contains details of
	 *  error, if not.
	 */
	function validateForm()
	{
		$db = e107::getDb();
		$tp = e107::getParser();

		$anonPost = e107::getPref('anon_post', false);

		// If user is Anonymous, but Anonymous posting is disabled.
		if(!USER && !$anonPost)
		{
			return array(
				'status'  => 'error',
				'message' => LAN_NCB_FRONT_06,
			);
		}

		// If user is Anonymous, and Anonymous posting is enabled, but nickname is missing.
		if(!USER && $anonPost && (!isset($_POST['nickname']) || empty($_POST['nickname'])))
		{
			return array(
				'status'  => 'error',
				'message' => LAN_NCB_FRONT_09,
			);
		}

		// If user is Anonymous, and Anonymous posting is enabled, and nickname is exits.
		if(!USER && $anonPost && (isset($_POST['nickname'])))
		{
			// Check nickname's ownership.
			$nick = trim(preg_replace("#\[.*\]#si", "", $tp->toDB($_POST['nickname'])));
			if($db->select("user", "*", "user_name='$nick' "))
			{
				return array(
					'status'  => 'error',
					'message' => LAN_NCB_FRONT_13,
				);
			}
		}

		// If message is missing.
		if(!isset($_POST['message']) || empty($_POST['message']))
		{
			return array(
				'status'  => 'error',
				'message' => LAN_NCB_FRONT_07,
			);
		}
		else
		{
			// Validate message.
			$cb_message = $_POST['message'];
			$cb_message = preg_replace("#\[.*?\](.*?)\[/.*?\]#s", "\\1", $cb_message);
			if((strlen(trim($cb_message)) > 1000))
			{
				return array(
					'status'  => 'error',
					'message' => LAN_NCB_FRONT_12,
				);
			}
		}

		// TODO: floodprotect is deprecated.
		$fp = new floodprotect;
		if(!$fp->flood("nodejs_chatbox", "ncb_datestamp"))
		{
			$flood_msg = str_replace("[!FLOODPROTECT]", FLOODPROTECT, LAN_NCB_FRONT_14);
			return array(
				'status'  => 'error',
				'message' => $flood_msg,
			);
		}

		return array();
	}


	/**
	 * Save new chatbox message, and trigger event.
	 */
	function saveMessage()
	{
		$db = e107::getDb('nodejs_chatbox');
		$tp = e107::getParser();
		$ip = e107::getIPHandler()->getIP(false);

		if(!USER && isset($_POST['nickname']))
		{
			$nick = 0 . '.' . trim(preg_replace("#\[.*\]#si", "", $tp->toDB($_POST['nickname'])));
		}
		else
		{
			$nick = USERID . '.' . USERNAME;
		}

		$cb_message = $_POST['message'];
		$cb_message = preg_replace("#\[.*?\](.*?)\[/.*?\]#s", "\\1", $cb_message);

		$insert = array(
			'ncb_id'        => 0,
			'ncb_nick'      => $nick,
			'ncb_message'   => $cb_message,
			'ncb_datestamp' => time(),
			'ncb_blocked'   => 0,
			'ncb_ip'        => $ip,
		);

		$result = $db->insert("nodejs_chatbox", $insert);

		// BC. Set values for triggering v1 "cboxpost" event.
		// TODO: remove these elements after removed old event triggering
		$insert['cmessage'] = $cb_message;
		$insert['ip'] = $ip;

		// If user is logged in, we update user info.
		if(USER)
		{
			$db->gen('UPDATE #user SET user_chats=user_chats+1, user_lastpost=' . $insert['ncb_datestamp'] . ' WHERE user_id=' . USERID);
		}

		if($result)
		{
			// Get last inserted id.
			$insert['ncb_id'] = $result;

			$event = e107::getEvent();
			// BC. Trigger old, v1 event.
			// TODO: remove old event triggering?
			$event->trigger('cboxpost', $insert);
			// Trigger new event.
			// TODO: finalize new event name.
			$event->trigger('nodejs_chatbox_message_insert', $insert);

			// Clear cache.
			$cache = e107::getCache();
			$cache->clear('nodejs_chatbox');

			// If the request is an Ajax request, we prepare the response.
			if(e_AJAX_REQUEST || isset($_POST['ncb_sent_ajax']))
			{
				$message = array(
					'status'  => 'ok',
					'message' => $this->getMessage($insert['ncb_id']),
				);
			}
		}
		else
		{
			$message = array(
				'status'  => 'error',
				'message' => LAN_NCB_FRONT_08,
			);
		}

		// If the request is an Ajax request, we echos response.
		if(isset($message) && (e_AJAX_REQUEST || isset($_POST['ncb_sent_ajax'])))
		{
			echo json_encode($message);
			exit;
		}
	}
}


new nodejs_chatbox_menu();
