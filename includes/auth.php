<?php
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user_id(): ?string {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name(): ?string {
    return $_SESSION['user_name'] ?? null;
}
