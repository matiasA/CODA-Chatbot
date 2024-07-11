<?php
/*
Plugin Name: CODA Chatbot
Description: An AI chatbot plugin for WordPress.
Version: 1.0
Author: Your Name
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/class-coda-chatbot-settings.php';

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
