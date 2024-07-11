class CODA_Chatbot_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'AI Chatbot Settings',
            'AI Chatbot',
            'manage_options',
            'coda-chatbot-settings',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('coda_chatbot_option');
        ?>
        <div class="wrap">
            <h1>AI Chatbot Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('coda_chatbot_option_group');
                do_settings_sections('coda-chatbot-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'coda_chatbot_option_group',
            'coda_chatbot_option',
            array($this, 'sanitize')
        );

        add_settings_section(
            'setting_section_id',
            'Chatbot Settings',
            array($this, 'print_section_info'),
            'coda-chatbot-settings'
        );

        add_settings_field(
            'api_key',
            'OpenAI API Key',
            array($this, 'api_key_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );

        add_settings_field(
            'welcome_message',
            'Welcome Message',
            array($this, 'welcome_message_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );

        add_settings_field(
            'bot_avatar',
            'Bot Avatar URL',
            array($this, 'bot_avatar_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );

        add_settings_field(
            'bot_context',
            'Bot Context',
            array($this, 'bot_context_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );

        add_settings_field(
            'ai_model',
            'AI Model',
            array($this, 'ai_model_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );

        add_settings_field(
            'activate_chatbot',
            'Activate Chatbot',
            array($this, 'activate_chatbot_callback'),
            'coda-chatbot-settings',
            'setting_section_id'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        if (isset($input['api_key']))
            $new_input['api_key'] = sanitize_text_field($input['api_key']);

        if (isset($input['welcome_message']))
            $new_input['welcome_message'] = sanitize_text_field($input['welcome_message']);

        if (isset($input['bot_avatar']))
            $new_input['bot_avatar'] = sanitize_text_field($input['bot_avatar']);

        if (isset($input['bot_context']))
            $new_input['bot_context'] = sanitize_textarea_field($input['bot_context']);

        if (isset($input['ai_model']))
            $new_input['ai_model'] = sanitize_text_field($input['ai_model']);

        if (isset($input['activate_chatbot']))
            $new_input['activate_chatbot'] = sanitize_text_field($input['activate_chatbot']);

        return $new_input;
    }

    public function print_section_info() {
        print 'Enter your settings below:';
    }

    public function api_key_callback() {
        printf(
            '<input type="text" id="api_key" name="coda_chatbot_option[api_key]" value="%s" />',
            isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
        );
    }

    public function welcome_message_callback() {
        printf(
            '<input type="text" id="welcome_message" name="coda_chatbot_option[welcome_message]" value="%s" />',
            isset($this->options['welcome_message']) ? esc_attr($this->options['welcome_message']) : ''
        );
    }

    public function bot_avatar_callback() {
        printf(
            '<input type="text" id="bot_avatar" name="coda_chatbot_option[bot_avatar]" value="%s" />',
            isset($this->options['bot_avatar']) ? esc_attr($this->options['bot_avatar']) : ''
        );
    }

    public function bot_context_callback() {
        printf(
            '<textarea id="bot_context" name="coda_chatbot_option[bot_context]" rows="5" cols="50">%s</textarea>',
            isset($this->options['bot_context']) ? esc_attr($this->options['bot_context']) : ''
        );
    }

    public function ai_model_callback() {
        $models = array(
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-4-32k' => 'GPT-4-32k'
        );
        $selected_model = isset($this->options['ai_model']) ? esc_attr($this->options['ai_model']) : 'gpt-3.5-turbo';
        echo '<select id="ai_model" name="coda_chatbot_option[ai_model]">';
        foreach ($models as $model => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($model),
                selected($selected_model, $model, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function activate_chatbot_callback() {
        $checked = isset($this->options['activate_chatbot']) && $this->options['activate_chatbot'] === '1' ? 'checked' : '';
        printf(
            '<label class="switch"><input type="checkbox" id="activate_chatbot" name="coda_chatbot_option[activate_chatbot]" value="1" %s><span class="slider round"></span></label>',
            $checked
        );
    }
}

if (is_admin()) {
    $coda_chatbot_settings = new CODA_Chatbot_Settings();
}
