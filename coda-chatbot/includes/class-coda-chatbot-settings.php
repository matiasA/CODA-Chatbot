<?php
if (!defined('ABSPATH')) {
    exit;
}

class CODA_Chatbot_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
    }

    public function add_settings_page() {
        add_options_page('AI Chatbot Settings', 'AI Chatbot', 'manage_options', 'coda-chatbot', array($this, 'settings_page'));
    }

    public function register_settings() {
        register_setting('ai_chatbot_settings_group', 'ai_chatbot_enabled', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'yes'));
        register_setting('ai_chatbot_settings_group', 'ai_chatbot_api_key', 'sanitize_text_field');
        register_setting('ai_chatbot_settings_group', 'ai_chatbot_welcome_message', 'sanitize_text_field');
        register_setting('ai_chatbot_settings_group', 'ai_chatbot_bot_avatar', 'sanitize_text_field');
    }

    public function enqueue_media_uploader() {
        wp_enqueue_media();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#ai_chatbot_bot_avatar_button').click(function(e) {
                    e.preventDefault();
                    var image = wp.media({
                        title: 'Upload Image',
                        multiple: false
                    }).open()
                    .on('select', function() {
                        var uploaded_image = image.state().get('selection').first();
                        var image_url = uploaded_image.toJSON().url;
                        $('#ai_chatbot_bot_avatar').val(image_url);
                    });
                });
            });
        </script>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>AI Chatbot Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ai_chatbot_settings_group');
                do_settings_sections('ai_chatbot_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Enable Chatbot</th>
                        <td>
                            <select name="ai_chatbot_enabled">
                                <option value="yes" <?php selected(get_option('ai_chatbot_enabled', 'yes'), 'yes'); ?>>Yes</option>
                                <option value="no" <?php selected(get_option('ai_chatbot_enabled', 'yes'), 'no'); ?>>No</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td><input type="text" name="ai_chatbot_api_key" value="<?php echo esc_attr(get_option('ai_chatbot_api_key')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Welcome Message</th>
                        <td><input type="text" name="ai_chatbot_welcome_message" value="<?php echo esc_attr(get_option('ai_chatbot_welcome_message')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bot Avatar URL</th>
                        <td>
                            <input type="text" id="ai_chatbot_bot_avatar" name="ai_chatbot_bot_avatar" value="<?php echo esc_attr(get_option('ai_chatbot_bot_avatar')); ?>" class="regular-text" />
                            <button type="button" class="button" id="ai_chatbot_bot_avatar_button">Select Image</button>
                        </td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>
            </form>
        </div>
        <?php
    }
}
?>
