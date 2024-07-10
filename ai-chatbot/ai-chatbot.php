<?php
/*
Plugin Name: AI Chatbot
Description: A visually attractive AI chatbot for WordPress.
Version: 1.0
Author: Your Name
*/

// Evitar el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('AI_CHATBOT_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_URL', plugin_dir_url(__FILE__));

// Incluir archivos necesarios
require_once AI_CHATBOT_DIR . 'includes/class-ai-chatbot.php';
require_once AI_CHATBOT_DIR . 'includes/class-ai-chatbot-settings.php';

// Inicializar el plugin
function ai_chatbot_init() {
    new AI_Chatbot();
    new AI_Chatbot_Settings();
}
add_action('plugins_loaded', 'ai_chatbot_init');
?>
