<?php if (empty($chat)): ?>
    <div class="text-center text-muted mt-5">Belum ada pesan. Mulai percakapan sekarang!</div>
<?php else: ?>
    <?php foreach ($chat as $row): ?>
        <?php
        $is_self = ($row->user_id == $this->session->userdata('id'));
        $align_class = $is_self ? 'right' : 'left';
        $flex_align = $is_self ? 'align-items-end' : 'align-items-start';
        ?>
        <div class="chat-message-container <?= $align_class ?>">
            <div class="d-flex flex-column <?= $flex_align ?>" style="max-width: 85%;">
                <div class="chat-username <?= $align_class ?>"><?= htmlspecialchars($row->username) ?></div>
                <div class="chat-message <?= $align_class ?>">
                    <?= nl2br(htmlspecialchars($row->pesan)) ?>
                    <div
                        style="font-size: 0.65em; opacity: 0.8; margin-top: 4px; text-align: <?= $is_self ? 'right' : 'left' ?>;">
                        <?= date('H:i', strtotime($row->created_at)) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php
    $last_msg = end($chat);
    ?>
    <div id="chat-meta" data-last-id="<?= isset($last_msg->chat_id) ? $last_msg->chat_id : 0 ?>"
        data-last-user="<?= isset($last_msg->user_id) ? $last_msg->user_id : 0 ?>" style="display:none;"></div>
<?php endif; ?>