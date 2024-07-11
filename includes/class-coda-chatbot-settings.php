<?php
class CODA_Chatbot_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    public function add_plugin_page() {
        add_menu_page(
            'AI Chatbot Settings', // Page title
            'AI Chatbot', // Menu title
            'manage_options', // Capability
            'coda-chatbot-settings', // Menu slug
            array($this, 'display_plugin_admin_page'), // Function to display the page
            'dashicons-admin-comments', // Icon URL or Dashicon class
            6 // Position
        );
    }
    private function get_openai_models($api_key) {
        $response = wp_remote_get('https://api.openai.com/v1/models', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key
            )
        ));

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['data'])) {
            return array_filter($data['data'], function($model) {
                return strpos($model['id'], 'gpt') !== false;
            });
        }

        return array();
    }


    public function register_settings() {
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_api_key', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_welcome_message', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_bot_avatar', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_context', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_activate', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_ai_model', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_limit_conversations', array($this, 'sanitize'));
        register_setting('coda_chatbot_settings_group', 'coda_chatbot_limit_characters', array($this, 'sanitize'));
    }

    public function sanitize($input) {
        return is_array($input) ? array_map('sanitize_text_field', $input) : sanitize_text_field($input);
    }

    public function enqueue_admin_styles($hook) {
        if ($hook != 'toplevel_page_coda-chatbot-settings') {
            return;
        }
        wp_enqueue_style('coda_chatbot_admin_css', plugin_dir_url(__FILE__) . '../css/admin-style.css');
    }

    public function display_plugin_admin_page() {
        $api_key = get_option('coda_chatbot_api_key', '');
        $welcome_message = get_option('coda_chatbot_welcome_message', '');
        $bot_avatar = get_option('coda_chatbot_bot_avatar', '');
        $bot_context = get_option('coda_chatbot_context', '');
        $ai_model = get_option('coda_chatbot_ai_model', 'gpt-3.5-turbo');
        $activate_chatbot = get_option('coda_chatbot_activate', '0');
        $limit_conversations = get_option('coda_chatbot_limit_conversations', 10);
        $limit_characters = get_option('coda_chatbot_limit_characters', 300);

        // Obtener modelos de IA desde la API de OpenAI
        $models = $this->get_openai_models($api_key);

        ?>
        <div class="wrap">
            <h1>AI Chatbot Settings</h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('coda_chatbot_settings_group');
                do_settings_sections('coda_chatbot_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">OpenAI API Key</th>
                        <td><input type="text" name="coda_chatbot_api_key" value="<?php echo esc_attr($api_key); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Welcome Message</th>
                        <td><input type="text" name="coda_chatbot_welcome_message" value="<?php echo esc_attr($welcome_message); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bot Avatar URL</th>
                        <td><input type="text" name="coda_chatbot_bot_avatar" value="<?php echo esc_attr($bot_avatar); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bot Context</th>
                        <td><textarea name="coda_chatbot_context" rows="5" cols="50"><?php echo esc_attr($bot_context); ?></textarea></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">AI Model</th>
                        <td>
                            <select name="coda_chatbot_ai_model">
                                <?php if (empty($models)) : ?>
                                    <option value="gpt-3.5-turbo" <?php selected($ai_model, 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo</option>
                                    <option value="gpt-4" <?php selected($ai_model, 'gpt-4'); ?>>GPT-4</option>
                                    <option value="gpt-4-32k" <?php selected($ai_model, 'gpt-4-32k'); ?>>GPT-4-32k</option>
                                <?php else : ?>
                                    <?php foreach ($models as $model) : ?>
                                        <option value="<?php echo esc_attr($model['id']); ?>" <?php selected($ai_model, $model['id']); ?>><?php echo esc_html($model['id']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Activate Chatbot</th>
                        <td>
                            <label class="switch">
                                <input type="checkbox" name="coda_chatbot_activate" value="1" <?php checked(1, $activate_chatbot, true); ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Limit Conversations</th>
                        <td><input type="number" name="coda_chatbot_limit_conversations" value="<?php echo esc_attr($limit_conversations); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Limit Characters</th>
                        <td><input type="number" name="coda_chatbot_limit_characters" value="<?php echo esc_attr($limit_characters); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }


}

if (is_admin()) {
    $coda_chatbot_settings = new CODA_Chatbot_Settings();
}