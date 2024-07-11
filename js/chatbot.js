document.addEventListener('DOMContentLoaded', function() {
    const chatbot = document.getElementById('coda-chatbot');
    if (!chatbot) return; // Return if the chatbot container is not found

    const apiKey = codaChatbotOptions.apiKey;
    const welcomeMessage = codaChatbotOptions.welcomeMessage;
    const botAvatar = codaChatbotOptions.botAvatar || 'https://via.placeholder.com/40'; // Default avatar image
    const botContext = codaChatbotOptions.botContext; // Get the bot context

    chatbot.innerHTML = `
        <div class="chatbot-container">
            <div class="chatbot-header">
                <div style="display: flex; align-items: center;">
                    <img src="${botAvatar}" alt="Bot Avatar">
                    <div class="header-info">
                        <span>Chatbot</span>
                        <span class="status">Online</span>
                    </div>
                </div>
                <button class="minimize-btn">-</button>
            </div>
            <div class="chatbot-messages"></div>
            <div class="chatbot-input-container">
                <input type="text" class="chatbot-input" placeholder="Write a message...">
                <button class="chatbot-send-btn">➤</button>
            </div>
        </div>
    `;

    const messages = document.querySelector('.chatbot-messages');
    const input = document.querySelector('.chatbot-input');
    const sendBtn = document.querySelector('.chatbot-send-btn');
    const minimizeBtn = document.querySelector('.minimize-btn');

    // Load messages from localStorage
    function loadMessages() {
        const savedMessages = JSON.parse(localStorage.getItem('chatbotMessages')) || [];
        savedMessages.forEach(msg => {
            const messageElement = document.createElement('div');
            messageElement.className = msg.className;
            messageElement.innerHTML = `
                ${msg.img ? `<img src="${msg.img}" alt="Avatar">` : ''}
                <span>${msg.text}</span>
            `;
            messages.appendChild(messageElement);
        });
        messages.scrollTop = messages.scrollHeight;
    }

    // Save messages to localStorage
    function saveMessage(className, img, text) {
        const savedMessages = JSON.parse(localStorage.getItem('chatbotMessages')) || [];
        savedMessages.push({ className, img, text });
        localStorage.setItem('chatbotMessages', JSON.stringify(savedMessages));
    }

    function sendMessage() {
        const userMessage = input.value;
        if (userMessage.trim() === '') return;
        input.value = '';

        const userMsgElement = document.createElement('div');
        userMsgElement.className = 'user-message';
        userMsgElement.innerHTML = `
            <span>${userMessage}</span>
            <img src="${botAvatar}" alt="User Avatar">
        `;
        messages.appendChild(userMsgElement);
        saveMessage('user-message', botAvatar, userMessage);

        fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + apiKey
            },
            body: JSON.stringify({
                model: 'gpt-3.5-turbo',
                messages: [
                    { role: 'system', content: botContext }, // Add context as system message
                    { role: 'user', content: userMessage }
                ]
            })
        })
        .then(response => response.json())
        .then(data => {
            const botMsgElement = document.createElement('div');
            botMsgElement.className = 'bot-message';
            botMsgElement.innerHTML = `
                <img src="${botAvatar}" alt="Bot Avatar">
                <span>${data.choices[0].message.content}</span>
            `;
            messages.appendChild(botMsgElement);
            saveMessage('bot-message', botAvatar, data.choices[0].message.content);

            // Scroll to the bottom of the chat messages
            messages.scrollTop = messages.scrollHeight;
        })
        .catch(error => {
            const botMsgElement = document.createElement('div');
            botMsgElement.className = 'bot-message';
            botMsgElement.innerHTML = `
                <img src="${botAvatar}" alt="Bot Avatar">
                <span>Error: ${error.message}</span>
            `;
            messages.appendChild(botMsgElement);
            saveMessage('bot-message', botAvatar, `Error: ${error.message}`);

            // Scroll to the bottom of the chat messages
            messages.scrollTop = messages.scrollHeight;
        });
    }

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    minimizeBtn.addEventListener('click', function() {
        chatbot.classList.toggle('minimized');
    });

    chatbot.addEventListener('click', function(e) {
        if (chatbot.classList.contains('minimized')) {
            chatbot.classList.remove('minimized');
        }
    });

    // Load existing messages from localStorage
    loadMessages();
});
