document.addEventListener('DOMContentLoaded', function() {
    const chatbot = document.getElementById('coda-chatbot');
    if (!chatbot) return;

    const apiKey = codaChatbotOptions.apiKey;
    const welcomeMessage = codaChatbotOptions.welcomeMessage;
    const botAvatar = codaChatbotOptions.botAvatar || 'https://via.placeholder.com/40';
    const botContext = codaChatbotOptions.botContext;
    const aiModel = codaChatbotOptions.aiModel || 'gpt-3.5-turbo';
    const limitConversations = parseInt(codaChatbotOptions.limitConversations) || 10;
    const limitCharacters = parseInt(codaChatbotOptions.limitCharacters) || 300;

    let conversationHistory = [];

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
            <div class="chatbot-feedback hidden">
                <span>Was this response helpful?</span>
                <button class="feedback-btn" data-value="yes">👍</button>
                <button class="feedback-btn" data-value="no">👎</button>
            </div>
        </div>
    `;

    const messages = document.querySelector('.chatbot-messages');
    const input = document.querySelector('.chatbot-input');
    const sendBtn = document.querySelector('.chatbot-send-btn');
    const header = document.querySelector('.chatbot-header');
    const feedbackContainer = document.querySelector('.chatbot-feedback');
    const feedbackBtns = document.querySelectorAll('.feedback-btn');

    function loadMessages() {
        const savedMessages = JSON.parse(localStorage.getItem('chatbotMessages')) || [];
        savedMessages.forEach(msg => {
            const messageElement = createMessageElement(msg.className, msg.img, msg.text);
            messages.appendChild(messageElement);
        });
        messages.scrollTop = messages.scrollHeight;
        conversationHistory = savedMessages.map(msg => ({
            role: msg.className === 'user-message' ? 'user' : 'assistant',
            content: msg.text
        }));
    }

    function saveMessage(className, img, text) {
        const savedMessages = JSON.parse(localStorage.getItem('chatbotMessages')) || [];
        savedMessages.push({ className, img, text });
        if (savedMessages.length > limitConversations * 2) {
            savedMessages.splice(0, 2);
        }
        localStorage.setItem('chatbotMessages', JSON.stringify(savedMessages));
    }

    function createMessageElement(className, img, text) {
        const messageElement = document.createElement('div');
        messageElement.className = className;
        messageElement.innerHTML = `
            ${img ? `<img src="${img}" alt="Avatar">` : ''}
            <span>${text}</span>
        `;
        return messageElement;
    }

    async function sendMessage() {
        const userMessage = input.value.trim();
        if (userMessage === '' || userMessage.length > limitCharacters) return;
        input.value = '';

        const userMsgElement = createMessageElement('user-message', botAvatar, userMessage);
        messages.appendChild(userMsgElement);
        saveMessage('user-message', botAvatar, userMessage);

        conversationHistory.push({ role: 'user', content: userMessage });

        try {
            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + apiKey
                },
                body: JSON.stringify({
                    model: aiModel,
                    messages: [
                        { role: 'system', content: botContext },
                        ...conversationHistory
                    ]
                })
            });

            if (!response.ok) {
                throw new Error('API request failed');
            }

            const data = await response.json();
            const botMessage = data.choices[0].message.content;

            const botMsgElement = createMessageElement('bot-message', botAvatar, botMessage);
            messages.appendChild(botMsgElement);
            saveMessage('bot-message', botAvatar, botMessage);

            conversationHistory.push({ role: 'assistant', content: botMessage });

            showFeedback();
        } catch (error) {
            console.error('Error:', error);
            const errorMsg = createMessageElement('bot-message', botAvatar, `Error: ${error.message}`);
            messages.appendChild(errorMsg);
            saveMessage('bot-message', botAvatar, `Error: ${error.message}`);
        }

        messages.scrollTop = messages.scrollHeight;
    }

    function showFeedback() {
        feedbackContainer.classList.remove('hidden');
    }

    function hideFeedback() {
        feedbackContainer.classList.add('hidden');
    }

    function submitFeedback(value) {
        // Here you would typically send the feedback to your server
        console.log(`User feedback: ${value}`);
        hideFeedback();
    }

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    header.addEventListener('click', function(e) {
        if (!e.target.classList.contains('minimize-btn')) {
            chatbot.classList.toggle('minimized');
        }
    });

    feedbackBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            submitFeedback(this.dataset.value);
        });
    });

    loadMessages();

    // Display welcome message
    if (conversationHistory.length === 0) {
        const welcomeMsgElement = createMessageElement('bot-message', botAvatar, welcomeMessage);
        messages.appendChild(welcomeMsgElement);
        saveMessage('bot-message', botAvatar, welcomeMessage);
    }
    // Agregar esta función para alternar entre minimizado y maximizado
    function toggleChatbot() {
        chatbot.classList.toggle('minimized');
    }

    // Crear el ícono del chatbot para cuando está minimizado
    const chatbotIcon = document.createElement('div');
    chatbotIcon.className = 'chatbot-icon';
    chatbotIcon.innerHTML = '💬'; // Puedes cambiar esto por una imagen si lo prefieres
    chatbot.appendChild(chatbotIcon);

    // Agregar el evento de clic al ícono del chatbot
    chatbotIcon.addEventListener('click', toggleChatbot);

    // Modificar el evento de clic en el encabezado para incluir la minimización
    const chatbotHeader = chatbot.querySelector('.chatbot-header');
    if (chatbotHeader) {
        const minimizeBtn = chatbotHeader.querySelector('.minimize-btn');
        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Evitar que el clic se propague al encabezado
                toggleChatbot();
            });
        }
        
        chatbotHeader.addEventListener('click', function(e) {
            if (chatbot.classList.contains('minimized')) {
                toggleChatbot();
            }
        });
    }
});

