class CODA_Chatbot_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_plugin_page() {
        add_menu_page(
            'AI Chatbot Settings', // Page title
            'AI Chatbot', // Menu title
            'manage_options', // Capability
            'coda-chatbot-settings', // Menu slug
            array( $this, 'display_plugin_admin_page' ), // Function to display the page
            'dashicons-admin-comments', // Icon URL or Dashicon class
            6 // Position
        );
    }

    public function register_settings() {
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_api_key' );
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_welcome_message' );
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_bot_avatar' );
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_context' ); // Register the new context setting
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_activate' ); // Register the activate setting
        register_setting( 'coda_chatbot_settings_group', 'coda_chatbot_ai_model' ); // Register the AI model setting
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
                    <tr valign="top">
                        <th scope="row">AI Model</th>
                        <td>
                            <select name="coda_chatbot_ai_model">
                                <option value="gpt-3.5-turbo" <?php selected(get_option('coda_chatbot_ai_model'), 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo</option>
                                <option value="gpt-4" <?php selected(get_option('coda_chatbot_ai_model'), 'gpt-4'); ?>>GPT-4</option>
                                <option value="gpt-4-32k" <?php selected(get_option('coda_chatbot_ai_model'), 'gpt-4-32k'); ?>>GPT-4-32k</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Activate Chatbot</th>
                        <td>
                            <label class="switch">
                                <input type="checkbox" name="coda_chatbot_activate" value="1" <?php checked(1, get_option('coda_chatbot_activate'), true); ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <style>
        .switch {
          position: relative;
          display: inline-block;
          width: 60px;
          height: 34px;
        }

        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          transition: .4s;
        }

        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:checked + .slider:before {
          transform: translateX(26px);
        }

        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
        </style>
        <?php
    }
}

if ( is_admin() ) {
    $coda_chatbot_settings = new CODA_Chatbot_Settings();
}

