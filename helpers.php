<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Flash Message Functions

function flash($type, $message) {
    if (!isset($_SESSION['flashes'])) {
        $_SESSION['flashes'] = [];
    }
    $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
}

function display_flash() {
    if (!empty($_SESSION['flashes'])) {
        foreach ($_SESSION['flashes'] as $flash) {
            $class = $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-red-100 text-red-800 border-red-300';
            echo "<div class='border px-4 py-2 rounded mb-3 {$class}'>{$flash['message']}</div>";
        }
        unset($_SESSION['flashes']);
    }
}


  
