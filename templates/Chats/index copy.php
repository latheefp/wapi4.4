<?php // debug($session_id) 
?>
<div id="chat-box">
    <div id="chat-messages"></div>
    <div id="user-input">
        <input type="text" id="message-input" placeholder="Type your message here...">
        <button id="send-button">Send</button>
    </div>
</div>
<script>
    const socket = new WebSocket('ws://localhost:8080');

    // Handle WebSocket connection
    socket.onopen = () => {
        console.log('WebSocket connected');
        //register
        const message = {
            session_id: "<?= $session_id ?>", // Replace with the actual user ID
            type: "register",
        };
        socket.send(JSON.stringify(message));
        toastr.success('Client Registered');
        console.log("Registered");
    };

    //send session ID for registration.



    // Handle WebSocket connection error
    socket.onerror = (error) => {
        toastr.error('Registration failed');
        console.log('WebSocket error:', error);
        console.log('WebSocket not connected');
    };


    // Handle incoming messages
    socket.onmessage = (event) => {
        const message = JSON.parse(event.data);
        appendMessage(message.user_id, message.content, 'received');
    };

    // Send messages to the server
    document.getElementById('send-button').addEventListener('click', () => {
        const messageInput = document.getElementById('message-input');
        const messageContent = messageInput.value.trim();

        if (messageContent !== '') {
            const message = {
                user_id: "<?= $user_name ?>", // Replace with the actual user ID
                content: messageContent,
            };
            //   console.log(message)


            if (socket.readyState === WebSocket.OPEN) {
                console.log(message);
                socket.send(JSON.stringify(message));
                appendMessage(message.user_id, messageContent, 'sent');

                messageInput.value = '';
            } else {
                toastr.error('Communication Error: WebSocket connection failed.');
                console.error('WebSocket error:', error);
                console.log('WebSocket is not open. Message not sent.');
            }
        }
    });

    // Function to display messages on the chat interface
    function appendMessage(userId, content, messageType) {
        const chatMessages = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = messageType;
        messageDiv.innerText = `${userId}: ${content}`;
        chatMessages.appendChild(messageDiv);
    }
</script>