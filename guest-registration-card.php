<?php
/*
Plugin Name: Guest Registration Card
Description: Registers guests and sends them a virtual card with QR code
Version: 1.0
Author: Xeven Solutions
*/

if (!defined('ABSPATH')) exit;

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/registration-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/registration-process.php';
require_once plugin_dir_path(__FILE__) . 'includes/qr-generator.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-template.php';

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'grc_enqueue_scripts');
function grc_enqueue_scripts() {
    wp_enqueue_style('grc-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}

register_activation_hook(__FILE__, 'grc_create_guest_role');
function grc_create_guest_role() {
    add_role('guest', 'Guest', array(
        'read' => true,
        'level_0' => true
    ));
}