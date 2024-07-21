<?php
/*
Plugin Name: CODA Chatbot
Description: An AI chatbot plugin for WordPress with advanced features.
Version: 2.0
Author: Your Name
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/class-coda-chatbot-settings.php';

function enqueue_chatbot_scripts() {
    if (get_option('coda_chatbot_activate')) {
        wp_enqueue_style( 'coda-chatbot-css', plugin_dir_url( __FILE__ ) . 'css/chatbot.css' );
        wp_enqueue_script( 'coda-chatbot-js', plugin_dir_url( __FILE__ ) . 'js/chatbot.js', array('jquery'), null, true );
        wp_localize_script( 'coda-chatbot-js', 'codaChatbotOptions', array(
            'apiKey' => get_option('coda_chatbot_api_key'),
            'welcomeMessage' => get_option('coda_chatbot_welcome_message'),
            'botAvatar' => get_option('coda_chatbot_bot_avatar'),
            'botContext' => get_option('coda_chatbot_context'),
            'aiModel' => get_option('coda_chatbot_ai_model'),
            'limitConversations' => get_option('coda_chatbot_limit_conversations'),
            'limitCharacters' => get_option('coda_chatbot_limit_characters'),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('coda-chatbot-nonce')
        ));
    }
}

add_action( 'wp_enqueue_scripts', 'enqueue_chatbot_scripts' );

function add_chatbot_container() {
    if (get_option('coda_chatbot_activate')) {
        echo '<div id="coda-chatbot" class="minimized"></div>';
    }
}

add_action( 'wp_footer', 'add_chatbot_container' );

function coda_chatbot_save_feedback() {
    check_ajax_referer('coda-chatbot-nonce', 'nonce');
    
    $feedback = sanitize_text_field($_POST['feedback']);
    $conversation_id = sanitize_text_field($_POST['conversation_id']);
    
    // Here you would typically save the feedback to your database
    // For now, we'll just log it
    error_log("Feedback received for conversation $conversation_id: $feedback");
    
    wp_send_json_success();
}

add_action('wp_ajax_coda_chatbot_save_feedback', 'coda_chatbot_save_feedback');
add_action('wp_ajax_nopriv_coda_chatbot_save_feedback', 'coda_chatbot_save_feedback');

// Shortcode to insert chatbot in specific pages/posts
function coda_chatbot_shortcode() {
    if (get_option('coda_chatbot_activate')) {
        return '<div id="coda-chatbot" class="minimized"></div>';
    }
    return '';
}

add_shortcode('coda_chatbot', 'coda_chatbot_shortcode');