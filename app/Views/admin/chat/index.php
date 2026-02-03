<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-comments text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
    </div>
</div>

<div class="row g-0" style="height: calc(100vh - 200px);">
    <!-- Users List -->
    <div class="col-md-3 border-end">
        <div class="p-3 bg-light border-bottom">
            <h6 class="mb-0">Conversations</h6>
        </div>
        <div class="users-list" style="overflow-y: auto; height: calc(100% - 60px);">
            <?php foreach ($users as $user): ?>
                <div class="user-item p-3 border-bottom" 
                     data-user-id="<?= $user['id'] ?>"
                     data-user-name="<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>"
                     onclick="openChat(<?= $user['id'] ?>, '<?= esc($user['first_name'] . ' ' . $user['last_name']) ?>')">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary text-white rounded-circle me-2" 
                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                            <div class="text-muted small"><?= ucfirst($user['role']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="col-md-9 d-flex flex-column">
        <div id="chatHeader" class="p-3 bg-light border-bottom" style="display: none;">
            <h6 class="mb-0" id="chatUserName"></h6>
        </div>
        
        <div id="chatMessages" class="flex-grow-1 p-3" style="overflow-y: auto; display: none;">
            <!-- Messages will be loaded here -->
        </div>

        <div id="chatInput" class="p-3 border-top" style="display: none;">
            <div class="input-group">
                <input type="text" class="form-control" id="messageInput" 
                       placeholder="Tapez votre message..." onkeypress="handleKeyPress(event)">
                <button class="btn btn-primary" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </div>
        </div>

        <div id="emptyState" class="d-flex align-items-center justify-content-center h-100">
            <div class="text-center text-muted">
                <i class="fas fa-comments fa-4x mb-3"></i>
                <p>Sélectionnez une conversation pour commencer</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let currentConversationId = null;
let messageInterval = null;

function openChat(userId, userName) {
    currentUserId = userId;
    const myUserId = <?= session()->get('user_id') ?>;
    currentConversationId = Math.min(myUserId, userId) + '_' + Math.max(myUserId, userId);
    
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('chatHeader').style.display = 'block';
    document.getElementById('chatMessages').style.display = 'block';
    document.getElementById('chatInput').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    
    loadMessages();
    
    // Auto-refresh messages
    if (messageInterval) clearInterval(messageInterval);
    messageInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if (!currentConversationId) return;
    
    fetch('<?= base_url('admin/chat/getMessages') ?>?conversation_id=' + currentConversationId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('chatMessages');
            const myUserId = <?= session()->get('user_id') ?>;
            
            container.innerHTML = '';
            
            data.messages.forEach(msg => {
                const isMe = msg.sender_id == myUserId;
                const div = document.createElement('div');
                div.className = 'mb-3 ' + (isMe ? 'text-end' : '');
                
                div.innerHTML = `
                    <div class="d-inline-block ${isMe ? 'bg-primary text-white' : 'bg-light'} rounded px-3 py-2" 
                         style="max-width: 70%;">
                        ${!isMe ? '<strong>' + msg.sender_name + '</strong><br>' : ''}
                        ${msg.message}
                        <div class="small ${isMe ? 'text-white-50' : 'text-muted'} mt-1">
                            ${new Date(msg.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}
                        </div>
                    </div>
                `;
                
                container.appendChild(div);
            });
            
            container.scrollTop = container.scrollHeight;
        });
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentUserId) return;
    
    fetch('<?= base_url('admin/chat/sendMessage') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            recipient_id: currentUserId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages();
        }
    });
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (messageInterval) clearInterval(messageInterval);
});
</script>

<style>
.user-item {
    cursor: pointer;
    transition: background-color 0.2s;
}
.user-item:hover {
    background-color: #f8f9fa;
}
</style>

<?= $this->endSection() ?>
