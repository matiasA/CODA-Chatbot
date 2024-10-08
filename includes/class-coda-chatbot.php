<?php
if (!defined('ABSPATH')) {
    exit;
}

class CODA_Chatbot {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_chatbot_to_footer'));
    }

    public function enqueue_scripts() {
        if (get_option('coda_chatbot_enabled', 'yes') !== 'yes') {
            return;
        }

        wp_enqueue_script('coda-chatbot-script', CODA_CHATBOT_URL . 'js/chatbot.js', array('jquery'), null, true);
        wp_enqueue_style('coda-chatbot-style', CODA_CHATBOT_URL . 'css/chatbot.css');

        $api_key = get_option('coda_chatbot_api_key', '');
        $welcome_message = get_option('coda_chatbot_welcome_message', 'Hello! How can I help you today?');
        $bot_avatar = get_option('coda_chatbot_bot_avatar', 'https://via.placeholder.com/40'); // Default avatar image

        wp_localize_script('coda-chatbot-script', 'codaChatbotOptions', array(
            'apiKey' => esc_js($api_key),
            'welcomeMessage' => esc_js($welcome_message),
            'botAvatar' => esc_js($bot_avatar),
        ));
    }

    public function add_chatbot_to_footer() {
        if (get_option('coda_chatbot_enabled', 'yes') !== 'yes') {
            return;
        }
        echo '<div id="coda-chatbot"></div>';
    }
}
?>
