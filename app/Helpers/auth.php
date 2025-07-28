<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('current_user')) {
    function current_user()
    {
        return Auth::user();
    }
}

if (!function_exists('is_staff')) {
    function is_staff()
    {
        return current_user()?->is_admin == 0;
    }
}

if (!function_exists('subdomain')) {
    function subdomain()
    {
        return current_user()?->subdomain;
    }
}

