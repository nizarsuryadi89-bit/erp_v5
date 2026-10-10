<style>
    :root {
        --chat-bg: #efe7dd;
        /* WhatsApp Beige */
        --chat-header-bg: #008069;
        /* WhatsApp Teal */
        --chat-header-text: #ffffff;
        --msg-left-bg: #ffffff;
        --msg-left-text: #111b21;
        --msg-right-bg: #d9fdd3;
        /* WhatsApp Green */
        --msg-right-text: #111b21;
        --widget-width: 380px;
        --widget-height: 550px;
        --chat-shadow: 0 1px 0.5px rgba(11, 20, 26, .13);
    }

    /* Fixed Widget Container */
    /* Header-Triggered Widget Container */
    .chat-widget-container {
        position: fixed;
        top: 70px;
        right: 20px;
        width: var(--widget-width);
        z-index: 9999;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
        display: none;
        /* Hidden by default */
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Minimized State adjustments */
    .chat-widget-container.minimized .chat-header {
        padding: 0;
        justify-content: center;
        height: 100%;
        background: transparent;
    }

    .chat-widget-container.minimized .chat-header-title,
    .chat-widget-container.minimized .chat-minimize-btn {
        display: none;
    }

    .chat-widget-container.minimized .chat-header-icon {
        display: flex;
        font-size: 1.5rem;
        margin: 0;
    }

    .chat-box-wrapper {
        display: flex;
        flex-direction: column;
        height: var(--widget-height);
        background-color: var(--chat-bg);
    }

    .chat-widget-container.minimized .chat-box-wrapper {
        height: 100%;
    }

    /* Header */
    .chat-header {
        background: var(--chat-header-bg);
        color: var(--chat-header-text);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: all 0.3s ease;
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chat-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-header-title {
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: -0.01em;
    }

    .chat-status-indicator {
        width: 8px;
        height: 8px;
        background-color: #4ade80;
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
    }

    .chat-minimize-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transition: background 0.2s;
        font-size: 0.8rem;
    }

    .chat-minimize-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Body */
    .chat-body-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background-color: var(--chat-bg);
        position: relative;
    }

    .chat-body {
        flex: 1;
        padding: 20px 16px;
        /* Slightly less side padding */
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 2px;
        /* Tighter grouping like WA */
        background-color: var(--chat-bg);
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        /* WA Doodle Pattern */
        background-repeat: repeat;
        background-size: 400px;
        opacity: 1;
        /* Ensure bg is visible */
    }

    /* Footer / Input Area */
    .chat-footer {
        padding: 8px 10px;
        background: #f0f2f5;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        background: #ffffff;
        border-radius: 8px;
        /* Slightly less round */
        padding: 6px 12px;
        transition: none;
        border: 1px solid #ffffff;
    }

    .input-wrapper:focus-within {
        background: #ffffff;
        border-color: #ffffff;
        box-shadow: none;
    }

    .chat-input {
        border: none;
        background: transparent;
        flex: 1;
        font-size: 0.95rem;
        padding: 8px 4px;
        outline: none;
        min-width: 0;
        color: #111b21;
    }

    .chat-input::placeholder {
        color: #9ca3af;
    }

    .btn-send-icon {
        background: transparent;
        color: #54656f;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s, background 0.2s;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .btn-send-icon:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #008069;
        transform: none;
    }

    .btn-send-icon:active {
        transform: scale(0.95);
    }

    .btn-send-icon i {
        font-size: 0.9rem;
        margin-left: -2px;
        /* Visual correction for paper plane icon */
    }

    /* Message Bubbles */
    .chat-message-container {
        display: flex;
        width: 100%;
        margin-bottom: 2px;
        /* Very tight spacing */
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-message-container.left {
        justify-content: flex-start;
    }

    .chat-message-container.right {
        justify-content: flex-end;
    }

    .chat-message {
        display: inline-block;
        /* Natural width */
        width: auto;
        min-width: 30px;
        /* Prevent 1-char collapse */
        max-width: 100%;
        /* Fit within parent */
        padding: 6px 7px 8px 9px;
        border-radius: 7.5px;
        font-size: 0.9rem;
        line-height: 19px;
        position: relative;
        word-wrap: break-word;
        /* Ensure wrapping only when needed */
        word-break: break-word;
        /* Support modern wrap */
        box-shadow: var(--chat-shadow);
    }

    .chat-message.left {
        background: var(--msg-left-bg);
        color: var(--msg-left-text);
        border-bottom-left-radius: 7.5px;
        border-top-left-radius: 0px;
        /* Tail effect hints */
    }

    .chat-message.right {
        background: var(--msg-right-bg);
        color: var(--msg-right-text);
        border-bottom-right-radius: 7.5px;
        border-top-right-radius: 0px;
        /* Tail effect hints */
        box-shadow: var(--chat-shadow);
        margin-right: 0;
    }

    .chat-username {
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 2px;
        padding: 0;
        color: #e542a3;
        /* WA Random color style, usually varies. Sticking to a distinct one or removing if direct DM */
        /* For Group chat look: */
    }

    .chat-username.right {
        display: none;
    }

    /* Scrollbar */
    .chat-body::-webkit-scrollbar {
        width: 5px;
    }

    .chat-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-body::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .chat-body::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 576px) {
        .chat-widget-container {
            width: 100%;
            height: calc(100vh - 60px);
            top: 60px;
            left: 0;
            right: 0;
            border-radius: 0;
        }
    }
</style>

<!-- Widget Container -->
<div class="chat-widget-container" id="chatWidget">
    <div class="chat-box-wrapper">
        <div class="chat-header" onclick="toggleChat()">
            <div class="chat-header-left">
                <div class="chat-header-icon" id="headerIcon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="chat-header-title">
                    Obrolan Tim <span class="chat-status-indicator"></span>
                </div>
            </div>
            <div class="chat-minimize-btn" id="chat-toggle-icon">
                <i class="fa fa-chevron-down"></i>
            </div>
        </div>

        <div class="chat-body-container">
            <div class="chat-body" id="chat-box">
                <!-- Content loaded via AJAX -->
            </div>

            <div class="chat-footer">
                <form id="chat-form" autocomplete="off">
                    <div class="input-wrapper">
                        <input type="text" name="pesan" class="chat-input" placeholder="Ketik pesan..." required>
                        <button type="submit" class="btn-send-icon">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Notification Sound -->
<audio id="chat-notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2354/2354-preview.mp3"
    preload="auto"></audio>

<script>

    // --- Widget Logic ---
    const chatWidget = document.getElementById('chatWidget');
    const toggleIcon = document.getElementById('chat-toggle-icon');
    const headerTitle = document.querySelector('.chat-header-title');

    function toggleChat() {
        const isVisible = chatWidget.style.display === 'block';

        if (isVisible) {
            $(chatWidget).fadeOut(200);
        } else {
            $(chatWidget).fadeIn(300);
            // Clear notification badge
            const badge = document.getElementById('chat-header-badge');
            if (badge) badge.classList.add('d-none');

            // Restore Scroll
            setTimeout(() => {
                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            }, 100);

            loadChat(true);
        }
    }

    // --- Chat Logic ---

    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);

    let lastMessageId = 0;
    const currentUserId = <?= (int)$this->session->userdata('id') ?>;

    function loadChat(force = false) {
        // Optimization: Stop ajax if minimized to save resources, UNLESS forced (e.g. on open)
        // OR we want to poll periodically even when minimized for notifications?
        // User requested NOTIFICATIONS. So we MUST poll even if minimized.
        // Reverting the "return if minimized" logic, but maybe poll slower?

        // Constant 5s poll is fine for modern servers.
        const chatWidget = document.getElementById('chatWidget');
        const isMinimized = chatWidget && chatWidget.classList.contains('minimized');

        $.ajax({
            url: '<?= base_url("chat/load/") ?>',
            method: 'GET',
            data: { last_id: lastMessageId },
            success: function (html) {
                if (html === "NO_CHANGE") return;

                // Parse HTML to get meta
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                var meta = tempDiv.querySelector('#chat-meta');

                if (meta) {
                    var fetchedId = parseInt(meta.getAttribute('data-last-id'));
                    var fetchedUser = parseInt(meta.getAttribute('data-last-user'));
                    var chatBox = $('#chat-box'); // Declare here for use in all blocks

                    // Notification Logic
                    if (lastMessageId > 0 && fetchedId > lastMessageId) {
                        // New message detected
                        if (fetchedUser !== currentUserId) {
                            playNotification();
                            if (isMinimized) {
                                showBadge();
                            }
                        }
                    }

                    if (fetchedId > lastMessageId) {
                        lastMessageId = fetchedId;

                        // Update DOM
                        // Since we are replacing entire HTML, we just check scroll pos before replace
                        var isAtBottom = chatBox.scrollTop() + chatBox.innerHeight() >= chatBox[0].scrollHeight - 50;

                        chatBox.html(html);

                        if (isAtBottom || lastMessageId === fetchedId) {
                            // wait, lastMessageId === fetchedId is always true here. 
                            // We force scroll if it's new message essentially
                            chatBox.scrollTop(chatBox[0].scrollHeight);
                        }
                    } else if (chatBox.is(':empty')) {
                        chatBox.html(html); // Init load
                    }
                } else {
                    // No messages or error
                    $('#chat-box').html(html);
                }
            },
            error: function (xhr) {
                console.log("Chat poll error ignored");
            },
            complete: function () {
                scheduleNextPoll();
            }
        });
    }

    function playNotification() {
        const audio = document.getElementById('chat-notification-sound');
        if (audio) {
            audio.play().catch(e => console.log("Audio play failed (user interaction needed first):", e));
        }
    }

    function showBadge() {
        const badge = document.getElementById('chat-header-badge');
        if (badge) {
            badge.classList.remove('d-none');
        }
    }

    $('#chat-form').on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        var input = form.find('input[name="pesan"]');

        if ($.trim(input.val()) === '') return;

        var data = form.serialize();

        $.ajax({
            url: '<?= base_url("chat/send") ?>',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    form[0].reset();
                    loadChat(true); // Force immediate reload
                } else {
                    alert(res.message || "Gagal mengirim pesan");
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                // alert("Terjadi kesalahan saat mengirim pesan.");
            }
        });
    });

    // --- Adaptive Polling Logic ---
    let pollTimeout;
    const POLLING_ACTIVE = 5000;      // 5 detik saat widget terbuka
    const POLLING_MINIMIZED = 20000;  // 20 detik saat diminimize (sekedar cek notif)
    const POLLING_IDLE = 60000;       // 60 detik saat tab tidak aktif/background

    function scheduleNextPoll() {
        if (pollTimeout) clearTimeout(pollTimeout);

        let interval = POLLING_ACTIVE;

        if (document.hidden) {
            interval = POLLING_IDLE;
        } else if (chatWidget.style.display !== 'block') {
            interval = POLLING_MINIMIZED;
        }

        pollTimeout = setTimeout(() => {
            loadChat();
        }, interval);
    }

    // Listen for visibility change to update poll immediately
    document.addEventListener("visibilitychange", () => {
        if (!document.hidden && chatWidget.style.display === 'block') {
            loadChat(true); // Immediate update when user comes back
        } else {
            scheduleNextPoll(); // Reschedule with new interval
        }
    });

    loadChat(); // Initial load

</script>