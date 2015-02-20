<?php
/**
 * @file
 * Class installation to define shortcodes.
 */

if (!defined('e107_INIT'))
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


	function sc_body_attributes()
	{
		$height = (int) $this->plugPrefs['nodejs_chatbox_height'];
		return ' style="height : ' . $height . 'px; overflow : auto;"';
	}


	function sc_form()
	{
		$anonPost = e107::getPref('anon_post', true);

		if (!USER && !$anonPost)
		{
			$link = '<a href="' . e_SIGNUP . '">' . LAN_NODEJS_CHATBOX_03 . '</a>';
			$form = str_replace('[!LINK]', $link, LAN_NODEJS_CHATBOX_02);
			return '<p>' . $form . '</p>';
		}

		$frm = e107::getForm();
		$action = SITEURLBASE . e_PLUGIN_ABS . 'nodejs_chatbox/nodejs_chatbox.php';

		$form = $frm->open('nchatbox', 'post', $action, array(
			'class' => 'formclass',
		));

		if (!USER && $anonPost)
		{
			$form .= '<div class="form-group">';
			$form .= $frm->text('nickname', '', 100, array(
				'id' => 'ncnickname',
				'class' => 'form-control tbox span12',
				'placeholder' => LAN_NODEJS_CHATBOX_10,
			));
			$form .= '</div>';
		}

		$form .= '<div class="form-group">';
		$form .= $frm->textarea('message', '', 2, 80, array(
			'id' => 'ncmessage',
			'class' => 'form-control tbox span12',
			'placeholder' => LAN_NODEJS_CHATBOX_11,
		));
		$form .= '</div>';

		$form .= '<div class="form-group">';

		$form .= $frm->button('submit', 1, 'submit', LAN_NODEJS_CHATBOX_04, array(
			'id' => 'submitmessage',
		));

		if ($this->plugPrefs['nodejs_chatbox_emote'] && e107::getPref('smiley_activate', true))
		{
			$form .= $frm->button('button', 1, 'button', LAN_NODEJS_CHATBOX_05, array(
				'id' => 'showemotes',
			));

			// TODO: r_emote() is deprecated!
			$form .= '<div class="well" style="display:none" id="ncemote">' . r_emote() . '</div>';
		}

		$form .= '</div>';

		$form .= $frm->close();

		return $form;
	}


	function sc_avatar()
	{
		$tp = e107::getParser();
		$tp->thumbWidth = 40;
		$tp->thumbHeight = 40;

		return $tp->toAvatar($this->var);
	}


	function sc_user_link()
	{
		$uid = (int) $this->var['uid'];

		if ($uid === 0)
		{
			return $this->var['user_name'];
		}

		return '<a href="' . e_HTTP . 'user.php?id.' . $uid . '">' . $this->var['user_name'] . '</a>';
	}


	function sc_message()
	{
		$tp = e107::getParser();

		$emotes_active = $this->plugPrefs['nodejs_chatbox_emote'] ? 'USER_BODY, emotes_on' : 'USER_BODY, emotes_off';
		$wordwrap = $this->plugPrefs['nodejs_chatbox_wordwrap'];

		$message = $tp->toHTML($this->var['message'], false, $emotes_active, $this->var['uid'], $wordwrap);

		return $message;
	}


	function sc_posted()
	{
		return e107::getDate()->convert_date($this->var['posted'], "relative");
	}
}
