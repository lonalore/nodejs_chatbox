<?php
/**
 * @file
 * Class instantiation to prepare JavaScript configurations and include css/js
 * files to page header.
 */

if (!defined('e107_INIT')) {
  exit;
}

/**
 * Class nodejs_chatbox_e_header.
 */
class nodejs_chatbox_e_header {

  function __construct() {
    self::include_components();
  }

  /**
   * Include necessary CSS and JS files
   */
  function include_components() {
    e107::css('nodejs_chatbox', 'css/nodejs_chatbox.css');
  }

}

// Class instantiation.
new nodejs_chatbox_e_header;
