<?php

class EvCwApiUser {
    public static function getApiKey() {
        if (!is_admin()) { return; }
        $user = wp_get_current_user();
        $user->user_email;
    }
}