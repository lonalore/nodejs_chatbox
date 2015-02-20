<?php
/**
 * @file
 * Class to handle ajax requests and control moderating process.
 */

require_once('../../class2.php');

e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

e107::lan('nodejs_chatbox', false, true);

/**
 * Class nodejs_chatbox.
 */
class nodejs_chatbox
{

	/**
	 * Constructor function.
	 */
	function __construct()
	{
		if (e_AJAX_REQUEST)
		{
			$this->ajaxHandler();
		}
	}


	/**
	 * Handler Ajax requests.
	 */
	function ajaxHandler()
	{
		if (USERID == 0 && (!isset($_POST['nickname']) || empty($_POST['nickname'])))
		{
			if (!e107::getPref('anon_post', true))
			{
				$message = array(
					'status' => 'error',
					'message' => LAN_NODEJS_CHATBOX_06,
				);
			}
			else
			{
				$message = array(
					'status' => 'error',
					'message' => LAN_NODEJS_CHATBOX_09,
				);
			}

			echo nodejs_json_encode($message);
			exit;
		}

		if (!isset($_POST['message']) || empty($_POST['message']))
		{
			$message = array(
				'status' => 'error',
				'message' => LAN_NODEJS_CHATBOX_07,
			);

			echo nodejs_json_encode($message);
			exit;
		}

		$this->saveMessage();
	}


	/**
	 * Save new chatbox message, and trigger event.
	 */
	function saveMessage()
	{
		$db = e107::getDb('nodejs_chatbox');
		$tp = e107::getParser();

		$insert = array(
			'id' => 0,
			'uid' => USERID,
			'nickname' => isset($_POST['nickname']) ? $tp->toDB($_POST['nickname']) : '',
			'message' => $tp->toDB($_POST['message']),
			'posted' => time(),
			'status' => 1,
		);

		$result = $db->insert('nodejs_chatbox', $insert);

		// If user is logged in, we update user info.
		if (USER) {
			$db->gen('UPDATE #user SET user_chats=user_chats+1, user_lastpost=' . $insert['posted'] . ' WHERE user_id=' . USERID);
		}

		if ($result)
		{
			// Get last inserted id.
			$insert['id'] = $result;

			$event = e107::getEvent();
			$event->trigger('nodejs_chatbox_message_insert', $insert);

			$message = array(
				'status' => 'ok',
				'message' => '',
			);

			echo nodejs_json_encode($message);
			exit;
		}
		else
		{
			$message = array(
				'status' => 'error',
				'message' => LAN_NODEJS_CHATBOX_08,
			);

			echo nodejs_json_encode($message);
			exit;
		}
	}
}

new nodejs_chatbox();
