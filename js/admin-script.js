jQuery(document).ready(function($) {
    // Handle the media uploader for the bot avatar
    $('#coda_chatbot_bot_avatar_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#coda_chatbot_bot_avatar').val(image_url);
            $('#coda_chatbot_bot_avatar_preview').html('<img src="' + image_url + '" style="max-width: 100px; height: auto;" />');
        });
    });

    // Preview the welcome message
    $('#coda_chatbot_welcome_message').on('input', function() {
        var welcomeMessage = $(this).val();
        $('#welcome_message_preview').text(welcomeMessage);
    });

    // Validate the API key
    $('#coda_chatbot_api_key').on('blur', function() {
        var apiKey = $(this).val();
        if (apiKey) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'validate_openai_api_key',
                    api_key: apiKey,
                    nonce: $('#coda_chatbot_settings_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        alert('API key is valid!');
                    } else {
                        alert('Invalid API key. Please check and try again.');
                    }
                }
            });
        }
    });
});