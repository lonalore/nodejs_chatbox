<?php
/**
 * @file
 * Register callback functions to events.
 */

e107_require_once(e_PLUGIN . 'nodejs/nodejs.main.php');

// Register events.
$event = e107::getEvent();
$event->register('cboxpost', 'nodejs_chatbox_event_cboxpost_callback');

function nodejs_chatbox_event_cboxpost_callback($edata_cb) {
	$message = (object) array(
		'channel' => 'nodejs_notify',
		'broadcast' => TRUE,
		'type' => 'chatboxMessage',
		'callback' => 'nodejsChatboxMenu',
		'data' => $edata_cb['cmessage'],
	);

	nodejs_enqueue_message($message);
}
