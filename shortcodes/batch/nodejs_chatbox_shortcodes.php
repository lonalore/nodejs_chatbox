<?php
/**
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Plugin - Chatbox
 *
 * @file
 * Class installation to define shortcodes.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_shortcodes.
 */
class nodejs_chatbox_shortcodes extends e_shortcode
{

	private $plugPrefs = array();


	function __construct()
	{
		$this->plugPrefs = e107::getPlugConfig('nodejs_chatbox')->getPref();
	}


	function sc_attributes()
	{
		// If chatbox is displayed inside a scrolling layer.
		if((int) $this->plugPrefs['ncb_layer'] === 1)
		{
			$height = (int) $this->plugPrefs['ncb_layer_height'];
			return ' style="height : ' . $height . 'px; overflow : auto;"';
		}
		return '';
	}


	function sc_form()
	{
		$anonPost = e107::getPref('anon_post', true);

		if(!USER && !$anonPost)
		{
			$link = '<a href="' . e_SIGNUP . '">' . LAN_NCB_FRONT_03 . '</a>';
			$form = str_replace('[!LINK]', $link, LAN_NCB_FRONT_02);
			return '<p>' . $form . '</p>';
		}

		$frm = e107::getForm();

		// Normal submit.
		$action = e_SELF . "?" . e_QUERY;

		// Ajax.
		if((int) $this->plugPrefs['ncb_handler'] === 1)
		{
			$action = SITEURLBASE . e_PLUGIN_ABS . "nodejs_chatbox/nodejs_chatbox_menu.php";
		}

		// Ajax with Node.js.
		if((int) $this->plugPrefs['ncb_handler'] === 2)
		{
			$action = SITEURLBASE . e_PLUGIN_ABS . "nodejs_chatbox/nodejs_chatbox_menu.php";
		}

		$form = $frm->open('nodejs_chatbox', 'post', $action, array(
			'class' => 'formclass',
			'id'    => 'nodejs_chatbox',
		));

		if(!USER && $anonPost)
		{
			$form .= '<div class="form-group">';
			$form .= $frm->text('nickname', '', 100, array(
				'id'          => 'ncb_nickname',
				'class'       => 'form-control tbox span12',
				'placeholder' => LAN_NCB_FRONT_10,
			));
			$form .= '</div>';
		}

		$form .= '<div class="form-group">';
		$form .= $frm->textarea('message', '', 2, 80, array(
			'id'          => 'ncb_message',
			'class'       => 'form-control tbox span12',
			'placeholder' => LAN_NCB_FRONT_11,
		));
		$form .= '</div>';

		$form .= '<div class="form-group">';
		$form .= $frm->button('submit', 1, 'submit', LAN_NCB_FRONT_04, array(
			'id' => 'ncb_submit',
		));

		if($this->plugPrefs['ncb_emote'] && e107::getPref('smiley_activate', true))
		{
			$form .= $frm->button('button', 1, 'button', LAN_NCB_FRONT_05, array(
				'id' => 'ncb_showemotes',
			));
			// TODO: r_emote() is deprecated!
			$form .= '<div class="well" style="display:none" id="ncb_emote">' . r_emote() . '</div>';
		}
		$form .= '</div>';

		$form .= $frm->hidden('ncb_sent', 1);

		if((int) $this->plugPrefs['ncb_handler'] === 1)
		{
			$form .= $frm->hidden('ncb_sent_ajax', 1);
		}

		$form .= $frm->close();
		return $form;
	}


	function sc_avatar()
	{
		$tp = e107::getParser();

		// TODO: ability to customize sizes on admin UI.
		$tp->thumbWidth = 40;
		$tp->thumbHeight = 40;

		return $tp->toAvatar($this->var);
	}


	function sc_user_link()
	{
		$uid = (int) $this->var['user_id'];

		if($uid === 0)
		{
			return $this->var['user_name'];
		}

		return '<a href="' . e_HTTP . 'user.php?id.' . $uid . '">' . $this->var['user_name'] . '</a>';
	}


	function sc_message()
	{
		if($this->var['ncb_blocked'])
		{
			return LAN_NCB_FRONT_20;
		}

		$tp = e107::getParser();

		$emotes_active = $this->plugPrefs['ncb_emote'] ? 'USER_BODY, emotes_on' : 'USER_BODY, emotes_off';
		$wordwrap = $this->plugPrefs['ncb_wordwrap'];

		$message = $tp->toHTML($this->var['ncb_message'], false, $emotes_active, $this->var['user_id'], $wordwrap);

		return $message;
	}


	function sc_posted()
	{
		$format = isset($this->plugPrefs['ncb_date_format']) ? $this->plugPrefs['ncb_date_format'] : 'relative';
		$date = e107::getDate();
		return $date->convert_date($this->var['ncb_datestamp'], $format);
	}


	function sc_moderate()
	{
		$db = e107::getDb();
		$chat_total = $db->count('nodejs_chatbox');
		$chatbox_posts = (int) $this->plugPrefs['ncb_posts'];

		$text = '';
		if($chat_total > $chatbox_posts || $this->var['moderate'])
		{
			$text .= '<br />';
			$text .= '<div style="text-align:center">';
			$text .= '<a href="' . e_PLUGIN_ABS . 'nodejs_chatbox/nodejs_chatbox.php">' . ($this->var['moderate'] ? LAN_NCB_FRONT_17 : LAN_NCB_FRONT_18) . '</a> (' . $chat_total . ')';
			$text .= '</div>';
		}

		return $text;
	}
}
