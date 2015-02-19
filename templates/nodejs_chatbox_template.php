<?php
/**
 * @file
 * Templates for plugins displays.
 */

$NODEJS_CHATBOX_TEMPLATE['HEADER'] = '<div class="nodejs-chatbox-header">';
$NODEJS_CHATBOX_TEMPLATE['HEADER'] .= '{FORM}';
$NODEJS_CHATBOX_TEMPLATE['HEADER'] .= '</div>';
$NODEJS_CHATBOX_TEMPLATE['HEADER'] .= '<div class="nodejs-chatbox-body"{BODY_ATTRIBUTES}>';
$NODEJS_CHATBOX_TEMPLATE['HEADER'] .= '<ul class="media-list unstyled">';

$NODEJS_CHATBOX_TEMPLATE['BODY'] = '<li class="media">';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '<span class="media-object pull-left">{AVATAR}</span>';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '<div class="media-body">';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '<strong>{USER_LINK}</strong>&nbsp;';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '<small class="muted smalltext">{POSTED}</small><br />';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '<p>{MESSAGE}</p>';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '</div>';
$NODEJS_CHATBOX_TEMPLATE['BODY'] .= '</li>';

$NODEJS_CHATBOX_TEMPLATE['FOOTER'] = '</ul>';
$NODEJS_CHATBOX_TEMPLATE['FOOTER'] .= '</div>';
$NODEJS_CHATBOX_TEMPLATE['FOOTER'] .= '<div class="nodejs-chatbox-footer">';
$NODEJS_CHATBOX_TEMPLATE['FOOTER'] .= '{MODERATE}';
$NODEJS_CHATBOX_TEMPLATE['FOOTER'] .= '</div>';
