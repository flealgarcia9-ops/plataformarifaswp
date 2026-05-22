<?php
/**
 * Funciones seguras que verifican si WooCommerce está activo
 */

// Verificar si WooCommerce está activo
if (!function_exists('is_woocommerce_active')) {
    function is_woocommerce_active() {
        return class_exists('WooCommerce');
    }
}

// Función segura para is_cart
if (!function_exists('safe_is_cart')) {
    function safe_is_cart() {
        return is_woocommerce_active() && function_exists('is_cart') && is_cart();
    }
}

// Función segura para is_checkout
if (!function_exists('safe_is_checkout')) {
    function safe_is_checkout() {
        return is_woocommerce_active() && function_exists('is_checkout') && is_checkout();
    }
}

// Función segura para wc_get_cart_url
if (!function_exists('safe_wc_get_cart_url')) {
    function safe_wc_get_cart_url() {
        if (is_woocommerce_active() && function_exists('wc_get_cart_url')) {
            return wc_get_cart_url();
        }
        return home_url('/');
    }
}

// Función segura para wc_add_notice
if (!function_exists('safe_wc_add_notice')) {
    function safe_wc_add_notice(,  = 'notice') {
        if (is_woocommerce_active() && function_exists('wc_add_notice')) {
            wc_add_notice(, );
        }
    }
}

// Función segura para wc_get_product
if (!function_exists('safe_wc_get_product')) {
    function safe_wc_get_product() {
        if (is_woocommerce_active() && function_exists('wc_get_product')) {
            return wc_get_product();
        }
        return false;
    }
}
