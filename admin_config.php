<?php
/**
 * @file
 * Class installations to handle configuration forms on Admin UI.
 */

require_once('../../class2.php');

if (!getperms('P'))
{
	header('location:' . e_BASE . 'index.php');
	exit;
}

// [PLUGINS]/nodejs/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('nodejs_chatbox', true, true);

/**
 * Class nodejs_chatbox_admin.
 */
class nodejs_chatbox_admin extends e_admin_dispatcher
{

	protected $modes = array(
		'main' => array(
			'controller' => 'nodejs_chatbox_admin_ui',
			'path' => null,
		),
	);

	protected $adminMenu = array(
		'main/prefs' => array(
			'caption' => LAN_AC_NODEJS_CHATBOX_01,
			'perm' => 'P',
		),
	);

	protected $menuTitle = LAN_PLUGIN__NODEJS_CHATBOX_NAME;

}

/**
 * Class nodejs_chatbox_admin_ui.
 */
class nodejs_chatbox_admin_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN__NODEJS_CHATBOX_NAME;

	protected $pluginName = "nodejs_chatbox";

	protected $preftabs = array(
		LAN_AC_NODEJS_CHATBOX_01,
	);

	protected $prefs = array(
		'nodejs_chatbox_posts' => array(
			'title' => LAN_AC_NODEJS_CHATBOX_02,
			'type' => 'number',
			'data' => 'int',
			'tab' => 0,
		),
		'nodejs_chatbox_height' => array(
			'title' => LAN_AC_NODEJS_CHATBOX_03,
			'type' => 'number',
			'data' => 'int',
			'tab' => 0,
		),
		'nodejs_chatbox_emote' => array(
			'title' => LAN_AC_NODEJS_CHATBOX_04,
			'type' => 'boolean',
			'writeParms' => 'label=yesno',
			'data' => 'int',
			'tab' => 0,
		),
		'nodejs_chatbox_mod' => array(
			'title' => LAN_AC_NODEJS_CHATBOX_05,
			'type' => 'userclass',
			'data' => 'int',
			'writeParms' => 'classlist=nobody,main,admin,classes',
			'tab' => 0,
		),
	);

}

new nodejs_chatbox_admin();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
