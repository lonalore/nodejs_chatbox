<?php
/**
 * Node.js Chatbox plugin for e107 v2.
 *
 * @file
 * Templates for plugins displays.
 */

$NODEJS_CHATBOX_TEMPLATE['menu_start'] = '
<div>{FORM}</div>
<div{ATTRIBUTES}>
	<ul class="media-list unstyled">';
$NODEJS_CHATBOX_TEMPLATE['menu_item'] = '
		<li class="media">
			<span class="media-object pull-left">{AVATAR}</span>
			<div class="media-body">
				<strong>{USER_LINK}</strong>&nbsp;<small class="muted smalltext">{POSTED}</small>
				<br />
				<p>{MESSAGE}</p>
			</div>
		</li>';
$NODEJS_CHATBOX_TEMPLATE['menu_end'] = '
	</ul>
</div>
<div>{MODERATE}</div>';


$NODEJS_CHATBOX_TEMPLATE['moderate_start'] = '
<div>
	<ul class="media-list unstyled">';
$NODEJS_CHATBOX_TEMPLATE['moderate_item'] = '
		<li class="media">
			<span class="media-object pull-left">{AVATAR}</span>
			<div class="media-body">
				<strong>{USER_LINK}</strong>&nbsp;<small class="muted smalltext">{POSTED}</small>
				<br />
				<p>{MESSAGE}</p>
			</div>
		</li>';
$NODEJS_CHATBOX_TEMPLATE['moderate_end'] = '
	</ul>
</div>';
