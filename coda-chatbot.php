<?php
/*
Plugin Name: CODA Chatbot
Description: A visually attractive AI chatbot for WordPress.
Version: 1.0
Author: CODA.uno
*/

// Evitar el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('CODA_CHATBOT_DIR', plugin_dir_path(__FILE__));
define('CODA_CHATBOT_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once CODA_CHATBOT_DIR . 'includes/class-coda-chatbot.php';
require_once CODA_CHATBOT_DIR . 'includes/class-coda-chatbot-settings.php';

// Inicializar el plugin
function coda_chatbot_init() {
    new CODA_Chatbot();
    new CODA_Chatbot_Settings();
}
add_action('plugins_loaded', 'coda_chatbot_init');
function enqueue_chatbot_scripts() {
    wp_enqueue_script( 'coda-chatbot-js', plugin_dir_url( __FILE__ ) . 'js/chatbot.js', array('jquery'), null, true );
    wp_localize_script( 'coda-chatbot-js', 'codaChatbotOptions', array(
        'apiKey' => get_option('coda_chatbot_api_key'),
        'welcomeMessage' => get_option('coda_chatbot_welcome_message'),
        'botAvatar' => get_option('coda_chatbot_bot_avatar'),
        'botContext' => get_option('coda_chatbot_context') // Pass context to JavaScript
    ));
}

add_action( 'wp_enqueue_scripts', 'enqueue_chatbot_scripts' );
?>
