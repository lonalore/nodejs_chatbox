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
			'controller' => 'nodejs_chatbox_main_ui',
			'path' => null,
		),
		'posts' => array(
			'controller' => 'nodejs_chatbox_posts_ui',
			'path' => null,
		),
	);

	protected $adminMenu = array(
		'main/prefs' => array(
			'caption' => LAN_AC_NODEJS_CHATBOX_01,
			'perm' => 'P',
		),
		'posts/calc' => array(
			'caption' => LAN_AC_NODEJS_CHATBOX_06,
			'perm' => 'P',
		),
	);

	protected $menuTitle = LAN_PLUGIN__NODEJS_CHATBOX_NAME;

}

/**
 * Class nodejs_chatbox_main_ui.
 */
class nodejs_chatbox_main_ui extends e_admin_ui
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
		'nodejs_chatbox_user_addon' => array(
			'title' => LAN_AC_NODEJS_CHATBOX_09,
			'type' => 'boolean',
			'writeParms' => 'label=yesno',
			'data' => 'int',
			'tab' => 0,
		),
	);
}

class nodejs_chatbox_posts_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN__NODEJS_NOTIFY_NAME;
	protected $pluginName = "nodejs_chatbox";


	function calcPage()
	{
		$mes = e107::getMessage();
		$db = e107::getDb();
		$db->update("user", "user_chats = 0");

		$list = array();

		if (e107::isInstalled("chatbox_menu"))
		{
			$qry = "SELECT u.user_id AS uid, count(c.cb_nick) AS count FROM #chatbox AS c
		LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.cb_nick,'.',1) = u.user_id
		WHERE u.user_id > 0
		GROUP BY uid";

			if ($db->gen($qry))
			{
				while ($row = $db->fetch())
				{
					$list[$row['uid']] = $row['count'];
				}
			}
		}

		$qry = "SELECT nc.uid, count(nc.id) AS count FROM #nodejs_chatbox AS nc
		LEFT JOIN #user AS u ON nc.uid = u.user_id
		WHERE nc.uid > 0
		GROUP BY nc.uid";

		if ($db->gen($qry))
		{
			while ($row = $db->fetch())
			{
				if (array_key_exists($row['uid'], $list))
				{
					$list[$row['uid']] += $row['count'];
				}
				else
				{
					$list[$row['uid']] = $row['count'];
				}
			}
		}

		foreach ($list as $uid => $cnt)
		{
			$db->update("user", "user_chats = '{$cnt}' WHERE user_id = '{$uid}'");
		}

		$this->addTitle(LAN_AC_NODEJS_CHATBOX_07);
		$mes->addSuccess(LAN_AC_NODEJS_CHATBOX_08);
	}
}

new nodejs_chatbox_admin();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
