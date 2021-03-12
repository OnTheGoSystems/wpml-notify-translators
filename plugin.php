<?php
/*
Plugin Name: WPML Notify Translators
Plugin URI: http://wpml.org
Description: Sends an email to translators when an original post with translations is updated.
Version: 0.0.1
Author: Andrea Sciamanna
Author URI: https://www.onthegosystems.com/team/andrea-sciamanna/
*/

namespace WPML\TM;

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once __DIR__ . '/src/NotifyOnPostUpdate.php';

$wpml_notify_on_post_update = new Notify_On_Post_Update();
$wpml_notify_on_post_update->init_hooks();
