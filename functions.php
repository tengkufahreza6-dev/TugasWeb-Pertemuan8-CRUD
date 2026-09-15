<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper Sanitasi Output (XSS Protection)
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper Flash Message (Session-based)
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success' atau 'danger'
        'message' => $message
    ];
}

function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $msg = $_SESSION['flash_message']['message'];
        unset($_SESSION['flash_message']);
        
        $badgeColor = ($type === 'success') ? '#10b981' : '#ef4444';
        return "
        <div style='padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; background-color: {$badgeColor}; color: white; font-weight: 500;'>
            {$msg}
        </div>";
    }
    return '';
}