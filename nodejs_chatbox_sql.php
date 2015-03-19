CREATE TABLE nodejs_chatbox (
ncb_id int(10) unsigned NOT NULL auto_increment,
ncb_nick varchar(30) NOT NULL default '',
ncb_message text NOT NULL,
ncb_datestamp int(10) unsigned NOT NULL default '0',
ncb_blocked tinyint(3) unsigned NOT NULL default '0',
ncb_ip varchar(45) NOT NULL default '',
PRIMARY KEY  (ncb_id)
) ENGINE=MyISAM;
