<?php
/*
Plugin Name: CODA Chatbot
Description: An AI chatbot plugin for WordPress.
Version: 1.0
Author: Your Name
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/class-coda-chatbot-settings.php';

function enqueue_chatbot_scripts() {
    if (get_option('coda_chatbot_activate')) { // Check if the chatbot is activated
        wp_enqueue_style( 'coda-chatbot-css', plugin_dir_url( __FILE__ ) . 'css/chatbot.css' );
        wp_enqueue_script( 'coda-chatbot-js', plugin_dir_url( __FILE__ ) . 'js/chatbot.js', array('jquery'), null, true );
        wp_localize_script( 'coda-chatbot-js', 'codaChatbotOptions', array(
            'apiKey' => get_option('coda_chatbot_api_key'),
            'welcomeMessage' => get_option('coda_chatbot_welcome_message'),
            'botAvatar' => get_option('coda_chatbot_bot_avatar'),
            'botContext' => get_option('coda_chatbot_context') // Pass context to JavaScript
        ));
    }
}

add_action( 'wp_enqueue_scripts', 'enqueue_chatbot_scripts' );

function add_chatbot_container() {
    if (get_option('coda_chatbot_activate')) { // Check if the chatbot is activated
        echo '<div id="coda-chatbot" class="minimized"></div>';
    }
}


function coda_chatbot_display() {
    ?>
    <!-- Botón para abrir el chatbot -->
    <button id="open-chatbot-button">Abrir Chatbot</button>

    <!-- Contenedor del chatbot -->
    <div id="coda-chatbot-container" style="display: none;">
        <!-- Contenido del chatbot -->
        <button id="close-chatbot-button">Cerrar Chatbot</button>
        <!-- Aquí iría el contenido del chatbot -->
    </div>
    <?php
}
add_shortcode('wp_footer', 'coda_chatbot_display');


add_action( 'wp_footer', 'add_chatbot_container' );
?>