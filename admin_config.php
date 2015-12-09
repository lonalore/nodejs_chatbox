<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Class installations to handle configuration forms on Admin UI.
 */

require_once("../../class2.php");

if(!e107::isInstalled('nodejs_chatbox') || !getperms("P"))
{
	header("Location: " . e_BASE . "index.php");
	exit;
}

// [PLUGINS]/nodejs_chatbox/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('nodejs_chatbox', true, true);


/**
 * Class nodejs_chatbox_admin.
 */
class nodejs_chatbox_admin extends e_admin_dispatcher
{

	protected $modes = array(
		'main'  => array(
			'controller' => 'nodejs_chatbox_admin_main_ui',
			'path'       => null,
		),
		'prune' => array(
			'controller' => 'nodejs_chatbox_admin_prune_ui',
			'path'       => null,
		),
		'posts' => array(
			'controller' => 'nodejs_chatbox_admin_posts_ui',
			'path'       => null,
		),
	);

	protected $adminMenu = array(
		'main/prefs'  => array(
			'caption' => LAN_NCB_ADMIN_01,
			'perm'    => 'P',
		),
		'prune/posts' => array(
			'caption' => LAN_NCB_ADMIN_12,
			'perm'    => 'P',
		),
		'posts/calc'  => array(
			'caption' => LAN_NCB_ADMIN_13,
			'perm'    => 'P',
		),
	);

	protected $menuTitle = LAN_PLUGIN_NODEJS_CHATBOX_NAME;
}


/**
 * Class nodejs_chatbox_admin_main_ui.
 */
class nodejs_chatbox_admin_main_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_NODEJS_CHATBOX_NAME;
	protected $pluginName  = "nodejs_chatbox";
	protected $preftabs    = array(
		LAN_NCB_ADMIN_01,
	);
	protected $prefs       = array(
		'ncb_posts'   => array(
			'title'       => LAN_NCB_ADMIN_02,
			'description' => LAN_NCB_ADMIN_03,
			'type'        => 'number',
			'data'        => 'int',
			'tab'         => 0,
		),
		'ncb_mod'          => array(
			'title'      => LAN_NCB_ADMIN_04,
			'type'       => 'userclass',
			'data'       => 'int',
			'writeParms' => 'classlist=nobody,main,admin,classes',
			'tab'        => 0,
		),
		'ncb_layer'        => array(
			'title'      => LAN_NCB_ADMIN_05,
			'type'       => 'dropdown',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_NCB_ADMIN_06,
				1 => LAN_NCB_ADMIN_07,
			),
			'tab'        => 0,
		),
		'ncb_layer_height' => array(
			'title'      => LAN_NCB_ADMIN_09,
			'type'       => 'number',
			'data'       => 'int',
			'readParms'  => array(
				'post' => ' px',
			),
			'writeParms' => array(
				'post' => ' px',
			),
			'tab'        => 0,
		),
		'ncb_handler'      => array(
			'title'      => LAN_NCB_ADMIN_24,
			'type'       => 'dropdown',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_NCB_ADMIN_25,
				1 => LAN_NCB_ADMIN_08,
			  2 => LAN_NCB_ADMIN_26,
			),
			'tab'        => 0,
		),
		'ncb_emote'        => array(
			'title'      => LAN_NCB_ADMIN_10,
			'type'       => 'boolean',
			'writeParms' => 'label=yesno',
			'data'       => 'int',
			'tab'        => 0,
		),
		'ncb_user_addon'   => array(
			'title'      => LAN_NCB_ADMIN_11,
			'type'       => 'boolean',
			'writeParms' => 'label=yesno',
			'data'       => 'int',
			'tab'        => 0,
		),
		'ncb_date_format'  => array(
			'title'      => LAN_NCB_ADMIN_31,
			'type'       => 'dropdown',
			'data'       => 'str',
			'writeParms' => array(
				'short'    => LAN_NCB_ADMIN_27,
				'long'     => LAN_NCB_ADMIN_28,
				'forum'    => LAN_NCB_ADMIN_29,
				'relative' => LAN_NCB_ADMIN_30,
			),
			'tab'        => 0,
		),
	);
}


/**
 * Class nodejs_chatbox_admin_prune_ui.
 */
class nodejs_chatbox_admin_prune_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_NODEJS_CHATBOX_NAME;
	protected $pluginName  = "nodejs_chatbox";


	function init()
	{
		if(isset($_POST['prune']) && (int) $_POST['prune'] === 1)
		{
			$chatbox_prune = (int) $_POST['chatbox_prune'];
			$prunetime = time() - $chatbox_prune;

			$db = e107::getDb();
			$log = e107::getLog();
			$cache = e107::getCache();
			$mes = e107::getMessage();

			$db->delete("nodejs_chatbox", "ncb_datestamp < '{$prunetime}' ");
			$log->add('LAN_NCB_ADMIN_17', $chatbox_prune . ', ' . $prunetime, E_LOG_INFORMATIVE, '');
			$cache->clear("nodejs_chatbox");
			$mes->addSuccess(LAN_NCB_ADMIN_16);
		}
	}


	function postsPage()
	{
		$frm = e107::getForm();

		$action = 'admin_config.php?mode=main&action=prefs';

		$form = $frm->open('prune', 'post', $action);

		$options = array(
			0       => '',
			86400   => LAN_NCB_ADMIN_18,
			604800  => LAN_NCB_ADMIN_19,
			2592000 => LAN_NCB_ADMIN_20,
			1       => LAN_NCB_ADMIN_21,
		);

		$form .= '<div class="form-group">';
		$form .= '<label class="control-label col-sm-2" for="subject">' . LAN_NCB_ADMIN_23 . '</label>';
		$form .= '<div class="col-sm-10">';
		$form .= $frm->select('chatbox_prune', $options, 0, array(
			'class' => 'form-control tbox select',
			'id'    => 'chatbox_prune',
		));
		$form .= '</div>';
		$form .= '</div>';

		$form .= '<div class="form-group">';
		$form .= '<div class="col-sm-offset-2 col-sm-10">';
		$form .= $frm->button('submit', 1, 'submit', LAN_NCB_ADMIN_22, array(
			'id' => 'form-submit',
		));
		$form .= '</div>';
		$form .= '</div>';

		$form .= $frm->hidden('prune', 1);

		echo $form;
	}
}


/**
 * Class nodejs_chatbox_admin_posts_ui.
 */
class nodejs_chatbox_admin_posts_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_NODEJS_CHATBOX_NAME;
	protected $pluginName  = "nodejs_chatbox";


	function calcPage()
	{
		$mes = e107::getMessage();
		$db = e107::getDb();

		$db->update("user", "user_chats = 0");

		$list = array();

		$qry = "SELECT u.user_id AS uid, count(c.ncb_nick) AS count FROM #nodejs_chatbox AS c
		LEFT JOIN #user AS u ON SUBSTRING_INDEX(c.ncb_nick,'.',1) = u.user_id
		WHERE u.user_id > 0
		GROUP BY uid";

		if($db->gen($qry))
		{
			while($row = $db->fetch())
			{
				$list[$row['uid']] = $row['count'];
			}
		}

		foreach($list as $uid => $cnt)
		{
			$db->update("user", "user_chats = '{$cnt}' WHERE user_id = '{$uid}'");
		}

		$this->addTitle(LAN_NCB_ADMIN_14);
		$mes->addSuccess(LAN_NCB_ADMIN_15);
	}
}


new nodejs_chatbox_admin();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
