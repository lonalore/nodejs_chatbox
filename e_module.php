<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Register callback functions to events.
 */

// Register events.
$event = e107::getEvent();
$event->register('nodejs_chatbox_message_insert', 'nodejs_chatbox_event_callback');

/**
 * Callback function to push rendered html chatbox message to clients.
 *
 * @param $data
 *  Details of message. Full database row from "nodejs_chatbox" table.
 */
function nodejs_chatbox_event_callback($data)
{
	$plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();

	// If the selected handler is Ajax with Node.js. And nodejs plugin is
	// installed.
	if((int) $plugPrefs['ncb_handler'] === 2 && e107::isInstalled('nodejs'))
	{
		$html = nodejs_chatbox_render_message($data);

		if($html)
		{
			e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

			$message = (object) array(
				'channel'   => 'nodejs_notify',
				'broadcast' => true,
				'type'      => 'chatboxMessage',
				'callback'  => 'nodejsChatbox',
				'data'      => $html,
			);

			nodejs_send_message($message);
		}
	}
}

/**
 * Render HTML output of a chatbox message.
 *
 * @param $data
 *  Details of message. Full database row from "nodejs_chatbox" table.
 *
 * @return string $html
 *  Rendered HTML output.
 */
function nodejs_chatbox_render_message($data)
{
	$db = e107::getDb('nodejs_chatbox');
	$template = e107::getTemplate('nodejs_chatbox');
	$sc = e107::getScBatch('nodejs_chatbox', true);
	$tp = e107::getParser();

	$query = 'SELECT c.*, u.user_id, u.user_name, u.user_image FROM #nodejs_chatbox AS c ';
	$query .= 'LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,".",1) = u.user_id ';
	$query .= 'WHERE c.ncb_id=' . (int) $data['ncb_id'];
	$db->gen($query);

	$message = array();
	while($row = $db->fetch())
	{
		$message = $row;
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
