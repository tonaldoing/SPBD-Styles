<?php
/*
Plugin Name: Small Projects Bureau Development - Styles and Scripts
Description: Page Template - Stylesheet - JS 
Version: 2.5
Author: Small Projects Bureau Development
Author URL: https://smallprojectsbureau.pro/
Coded by: German Wainfeld - Martin Fuks - Tomas Vilas
*/

@require_once 'create-templates.php';
@require_once 'panel.php';
@require_once 'single-spbd_style.php';

// Encolar scripts de administrador
function spbd_enqueue_admin_scripts($hook_suffix) {
    global $post_type;

    if ($post_type == 'spbd_style') {
        wp_enqueue_script('spbd-admin-scripts', plugin_dir_url(__FILE__) . 'js/admin-scripts.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'spbd_enqueue_admin_scripts');

?>
