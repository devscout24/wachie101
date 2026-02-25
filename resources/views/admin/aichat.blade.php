@extends('backend.master')
@section('body')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #343541;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-container {
            width: 100%;
            max-width: 900px;
            height: 90vh;
            background: #343541;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            text-align: center;
            color: #ececf1;
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid #565869;
        }

        .chat-body {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }

        .chat-body::-webkit-scrollbar {
            width: 8px;
        }

        .chat-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background: #565869;
            border-radius: 4px;
        }

        .message-row {
            display: flex;
            gap: 16px;
            padding: 20px;
            border-radius: 8px;
        }

        .message-row.user {
            background: #343541;
        }

        .message-row.bot {
            background: #444654;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .avatar.user {
            background: #5436da;
            color: #fff;
        }

        .avatar.bot {
            background: #19c37d;
            color: #fff;
        }

        .message-text {
            color: #ececf1;
            font-size: 16px;
            line-height: 1.6;
            padding-top: 6px;
        }

        .typing {
            font-style: italic;
            color: #8e8ea0;
        }

        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #565869;
        }

        .input-wrapper {
            background: #40414f;
            border-radius: 12px;
            border: 1px solid #565869;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            gap: 12px;
        }

        .input-wrapper textarea {
            flex: 1;
            background: transparent;
            border: none;
            color: #ececf1;
            font-size: 16px;
            resize: none;
            outline: none;
            font-family: inherit;
            height: 24px;
            max-height: 200px;
        }

        .input-wrapper textarea::placeholder {
            color: #8e8ea0;
        }

        .send-btn {
            background: #19c37d;
            color: #fff;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .send-btn:hover {
            background: #1a9f6a;
        }

        .send-btn:disabled {
            background: #40414f;
            cursor: not-allowed;
        }
    </style>

    <div class="chat-container">
        <div class="chat-header">AI Assistant</div>

        <div class="chat-body" id="chatBody"></div>

        <div class="chat-input-area">
            <div class="input-wrapper">
                <textarea id="prompt" placeholder="Send a message..." rows="1"></textarea>
                <button class="send-btn" id="sendBtn" onclick="sendPrompt()">➤</button>
            </div>
        </div>
    </div>

    <script>
        function addMessage(text, type) {
            const chatBody = document.getElementById('chatBody');
            
            const row = document.createElement('div');
            row.classList.add('message-row', type);
            
            const avatar = document.createElement('div');
            avatar.classList.add('avatar', type);
            avatar.innerText = type === 'user' ? 'U' : 'AI';
            
            const messageText = document.createElement('div');
            messageText.classList.add('message-text');
            messageText.innerText = text;
            
            row.appendChild(avatar);
            row.appendChild(messageText);
            chatBody.appendChild(row);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function addTyping() {
            const chatBody = document.getElementById('chatBody');
            
            const row = document.createElement('div');
            row.classList.add('message-row', 'bot');
            row.id = 'typing';
            
            const avatar = document.createElement('div');
            avatar.classList.add('avatar', 'bot');
            avatar.innerText = 'AI';
            
            const messageText = document.createElement('div');
            messageText.classList.add('message-text', 'typing');
            messageText.innerText = 'Thinking...';
            
            row.appendChild(avatar);
            row.appendChild(messageText);
            chatBody.appendChild(row);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function removeTyping() {
            const typing = document.getElementById("typing");
            if (typing) typing.remove();
        }

        function sendPrompt() {
            const textarea = document.getElementById('prompt');
            const text = textarea.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            textarea.value = "";
            textarea.style.height = 'auto';

            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;

            addTyping();

            fetch("/openai-chat/send", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        prompt: text
                    })
                })
                .then(res => res.json())
                .then(data => {
                    removeTyping();
                    addMessage(data.reply, 'bot');
                    sendBtn.disabled = false;
                })
                .catch(err => {
                    removeTyping();
                    addMessage("Something went wrong. Please try again.", "bot");
                    sendBtn.disabled = false;
                });
        }

        // Auto-resize textarea
        const textarea = document.getElementById('prompt');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });

        // Enter to send
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendPrompt();
            }
        });
    </script>
@endsection