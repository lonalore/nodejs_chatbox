<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Chatbox e_search addon.
 */


if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class nodejs_chatbox_search.
 *
 * V2 e_search addon.
 */
class nodejs_chatbox_search extends e_search // include plugin-folder in the name.
{

	function config()
	{
		$search = array(
			'name'          => LAN_PLUGIN_NODEJS_CHATBOX_NAME,
			'table'         => 'nodejs_chatbox',
			'advanced'      => array(
				'date'   => array(
					'type' => 'date',
					'text' => LAN_DATE_POSTED,
				),
				'author' => array(
					'type' => 'author',
					'text' => LAN_SEARCH_61,
				),
			),
			'return_fields' => array(
				'ncb_id',
				'ncb_nick',
				'ncb_message',
				'ncb_datestamp',
			),
			'search_fields' => array(
				'ncb_nick'    => '1',
				'ncb_message' => '1',
			),
			// fields and weights.
			'order'         => array(
				'ncb_datestamp' => DESC,
			),
			'refpage'       => 'nodejs_chatbox.php',
		);

		return $search;
	}


	/**
	 * Compile Database data for output.
	 */
	function compile($row)
	{
		$tp = e107::getParser();

		preg_match("/([0-9]+)\.(.*)/", $row['ncb_nick'], $user);

		$res = array();

		$res['link'] = e_PLUGIN . "nodejs_chatbox/nodejs_chatbox.php?" . $row['ncb_id'] . ".fs";
		$res['pre_title'] = LAN_SEARCH_7;
		$res['title'] = $user[2];
		$res['summary'] = $row['ncb_message'];
		$res['detail'] = $tp->toDate($row['ncb_datestamp'], "long");

		return $res;
	}


	/**
	 * Optional - Advanced Where
	 *
	 * @param array $parm
	 *  Data returned from $_GET (ie. advanced fields included. in this case
	 *  'date' and 'author')
	 *
	 * @return string $qry
	 */
	function where($parm = array())
	{
		$tp = e107::getParser();

		$qry = "";

		if(vartrue($parm['time']) && is_numeric($parm['time']))
		{
			$qry .= " ncb_datestamp " . ($parm['on'] == 'new' ? '>=' : '<=') . " '" . (time() - $parm['time']) . "' AND";
		}

		if(vartrue($parm['author']))
		{
			$qry .= " ncb_nick LIKE '%" . $tp->toDB($parm['author']) . "%' AND";
		}

		return $qry;
	}


}
