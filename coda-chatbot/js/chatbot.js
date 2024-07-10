document.addEventListener('DOMContentLoaded', function() {
    const chatbot = document.getElementById('ai-chatbot');
    const apiKey = aiChatbotOptions.apiKey;
    const welcomeMessage = aiChatbotOptions.welcomeMessage;
    const botAvatar = aiChatbotOptions.botAvatar || 'https://via.placeholder.com/40'; // Default avatar image

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
            <div class="chatbot-messages">
                <div class="bot-message">
                    <img src="${botAvatar}" alt="Bot Avatar">
                    <span>${welcomeMessage}</span>
                </div>
            </div>
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

    function sendMessage() {
        const userMessage = input.value;
        if (userMessage.trim() === '') return;
        input.value = '';

        const userMsgElement = document.createElement('div');
        userMsgElement.className = 'user-message';
        userMsgElement.innerText = userMessage;
        messages.appendChild(userMsgElement);

        fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + apiKey
            },
            body: JSON.stringify({
                model: 'gpt-3.5-turbo',
                messages: [{ role: 'user', content: userMessage }]
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
        minimizeBtn.textContent = chatbot.classList.contains('minimized') ? '+' : '-';
    });
});
