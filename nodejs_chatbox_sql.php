CREATE TABLE `nodejs_chatbox` (
`id` int(10) unsigned NOT NULL auto_increment,
`uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The uid of the user.',
`message` text NOT NULL COMMENT 'Message posted by user.',
`posted` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The timestamp of when the user posted the message.',
`status` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Status of message. Active(1). Inactive(0).',
UNIQUE KEY `id` (`id`),
KEY `uid` (`uid`),
KEY `posted` (`posted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Table to store chatbox messages.';
