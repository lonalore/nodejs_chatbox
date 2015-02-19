CREATE TABLE `nodejs_chatbox` (
`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The uid of the user.',
`posted` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The timestamp of when the user posted the message.',
`status` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Status of message. Active(1). Inactive(0).',
UNIQUE KEY `uid` (`uid`),
KEY `login_time` (`posted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Table to store chatbox messages.';
