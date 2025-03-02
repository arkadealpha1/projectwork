// Chat button functionality
// document.getElementById('chat-button').addEventListener('click', function() {
//     const chatDropdown = document.getElementById('chat-dropdown');
//     if (chatDropdown.style.display === 'block') {
//         chatDropdown.style.display = 'none';
//     } else {
//         chatDropdown.style.display = 'block';
//         fetchChatInbox();
//     }
// });
function openDropdown(){
    const chatDropdown = document.getElementById('chat-dropdown');
    if (chatDropdown.style.display === 'block') {
        chatDropdown.style.display = 'none';
    } else {
        chatDropdown.style.display = 'block';
        fetchChatInbox();
    }   
}
// Fetch chat inbox (connected users)
function fetchChatInbox() {
    fetch('../chat_connect/fetch_inbox.php')
        .then(response => response.json())
        .then(data => {
            console.log("11",data)
            const inboxList = document.getElementById('inbox-list');
            inboxList.innerHTML = '';
            data.forEach(user => {
                const listItem = document.createElement('li');
                listItem.textContent = user.username;
                listItem.addEventListener('click', () => {
                    window.location.href = `../chat_connect/chat.php?user2_id=${user.id}`;
                });
                inboxList.appendChild(listItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}