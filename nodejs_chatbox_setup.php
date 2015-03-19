<?php
/**
 * e107 website system
 *
 * Copyright (c) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @file
 * Custom install/uninstall/update routines.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_setup.
 */
class nodejs_chatbox_setup
{

	function install_pre($var)
	{
	}


	function install_post($var)
	{
	}


	function uninstall_options()
	{
	}


	function uninstall_post($var)
	{
	}


	/**
	 * Trigger an upgrade alert or not.
	 *
	 * @param array $var
	 *
	 * @return bool
	 *  True to trigger an upgrade alert, and false to not.
	 */
	function upgrade_required($var)
	{
	}


	function upgrade_pre($var)
	{
	}


	function upgrade_post($var)
	{
	}
}
