const chatBtn = document.getElementById('chat-btn');
const chatBox = document.getElementById('chat-box');
const sendBtn = document.getElementById('send-btn');
const chatInput = document.getElementById('chat-input');
const chatWindow = document.getElementById('chat-window');

chatBtn.addEventListener('click', () => {
  chatBox.classList.toggle('show');
});

sendBtn.addEventListener('click', () => {
  const message = chatInput.value.trim();
  if (message) {
    const userMsg = document.createElement('p');
    userMsg.innerHTML = `<strong>Resident:</strong> ${message}`;
    chatWindow.appendChild(userMsg);
    chatInput.value = '';
    chatWindow.scrollTop = chatWindow.scrollHeight;

    setTimeout(() => {
      const reply = document.createElement('p');
      reply.innerHTML = `<strong>Assistant:</strong> Thank you for reaching out. We'll assist you shortly!`;
      chatWindow.appendChild(reply);
      chatWindow.scrollTop = chatWindow.scrollHeight;
    }, 1000);
  }
});
