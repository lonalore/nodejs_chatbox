<?php
/**
 * @file
 * Register callback functions to events.
 */

e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

// Register events.
$event = e107::getEvent();
$event->register('nodejs_chatbox_message_insert', 'nodejs_chatbox_event_cboxpost_callback');

function nodejs_chatbox_event_cboxpost_callback($data)
{
	$template = e107::getTemplate('nodejs_chatbox');
	$sc = e107::getScBatch('nodejs_chatbox', true);
	$tp = e107::getParser();
	$db = e107::getDb('nodejs_chatbox');

	$query = 'SELECT nc.*, u.user_name, u.user_image FROM #nodejs_chatbox AS nc ';
	$query .= 'LEFT JOIN #user AS u ON nc.uid = u.user_id ';
	$query .= 'WHERE nc.id = ' . (int) $data['id'] . ' ';
	$query .= 'ORDER BY nc.posted DESC ';

	$db->gen($query);

	while ($row = $db->fetch())
	{
		$details = $row;
	}

	if (isset($details))
	{
		$converted = $details;
		$converted['uid'] = (int) $details['uid'];

		if ((int) $details['uid'] === 0) {
			$converted['user_name'] = $details['nickname'];
			$converted['user_image'] = '';
		}

		$sc->setVars($converted);
		$html = $tp->parseTemplate($template['BODY'], true, $sc);

		$message = (object) array(
			'channel' => 'nodejs_notify',
			'broadcast' => true,
			'type' => 'chatboxMessage',
			'callback' => 'nodejsChatbox',
			'data' => $html,
		);

		nodejs_send_message($message);
	}
}
