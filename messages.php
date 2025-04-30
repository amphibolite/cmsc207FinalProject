<h2>Chat with [Recipient]</h2>

<div id="chat-box"></div>

<form id="message-form">
    <input type="hidden" name="receiver_id" value="[RECEIVER_ID]">
    <textarea name="message" required></textarea>
    <button type="submit">Send</button>
</form>

<script>
    const form = document.getElementById('message-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        await fetch('send_message.php', {
            method: 'POST',
            body: formData
        });
        form.message.value = '';
        loadMessages(); // reload messages
    });

    async function loadMessages() {
        const response = await fetch('get_messages.php?receiver_id=[RECEIVER_ID]');
        const messages = await response.json();
        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML = messages.map(msg =>
            `<p><strong>${msg.sender_name}:</strong> ${msg.message}</p>`
        ).join('');
    }

    setInterval(loadMessages, 3000); // auto-refresh
    loadMessages();
</script>
