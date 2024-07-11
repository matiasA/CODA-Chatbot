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
        add_options_page('CODA Chatbot Settings', 'CODA Chatbot', 'manage_options', 'coda-chatbot', array($this, 'settings_page'));
    }

    public function register_settings() {
    register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_api_key' );
    register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_welcome_message' );
    register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_bot_avatar' );
    register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_context' ); // Register the new context setting
    }

    public function enqueue_media_uploader() {
        wp_enqueue_media();
        // Instead of directly outputting the script, we'll enqueue it to be output at the right time
        add_action('admin_footer', function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#coda_chatbot_bot_avatar_button').click(function(e) {
                        e.preventDefault();
                        var image = wp.media({
                            title: 'Select or Upload Image',
                            button: {
                                text: 'Use this image'
                            },
                            multiple: false
                        }).open()
                        .on('select', function() {
                            var uploaded_image = image.state().get('selection').first();
                            var image_url = uploaded_image.toJSON().url;
                            $('#coda_chatbot_bot_avatar').val(image_url);
                        });
                    });
                });
            </script>
            <?php
        });
    }

    public function display_plugin_admin_page() {
        ?>
        <div class="wrap">
            <h1>AI Chatbot Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'coda_chatbot_settings_group' );
                do_settings_sections( 'coda_chatbot_settings_group' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">OpenAI API Key</th>
                        <td><input type="text" name="coda_chatbot_api_key" value="<?php echo esc_attr( get_option('coda_chatbot_api_key') ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Welcome Message</th>
                        <td><input type="text" name="coda_chatbot_welcome_message" value="<?php echo esc_attr( get_option('coda_chatbot_welcome_message') ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bot Avatar URL</th>
                        <td><input type="text" name="coda_chatbot_bot_avatar" value="<?php echo esc_attr( get_option('coda_chatbot_bot_avatar') ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Bot Context</th>
                        <td><textarea name="coda_chatbot_context" rows="5" cols="50"><?php echo esc_attr( get_option('coda_chatbot_context') ); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
?>