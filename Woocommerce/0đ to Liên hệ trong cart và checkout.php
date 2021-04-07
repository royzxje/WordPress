<?php
/*
Author: levantoan.com
*/
add_filter('woocommerce_cart_item_subtotal', 'devvn_woocommerce_cart_item_subtotal');
add_filter('woocommerce_cart_item_price', 'devvn_woocommerce_cart_item_subtotal');
add_filter('woocommerce_cart_subtotal', 'devvn_woocommerce_cart_item_subtotal');
add_filter('woocommerce_cart_totals_order_total_html', 'devvn_woocommerce_cart_item_subtotal');
add_filter('woocommerce_order_formatted_line_subtotal', 'devvn_woocommerce_cart_item_subtotal');
add_filter('woocommerce_get_formatted_order_total', 'devvn_woocommerce_cart_item_subtotal');
function devvn_woocommerce_cart_item_subtotal($price_html){
    $price = intval(wp_strip_all_tags($price_html));
    if(!$price){
        return '<span class="woocommerce-Price-amount amount">Liên hệ</span>';
    }
    return $price_html;
}

add_filter('woocommerce_get_order_item_totals', 'devvn_woocommerce_get_order_item_totals');
function devvn_woocommerce_get_order_item_totals($total_rows){
    foreach ( $total_rows as $key => $total ) {
        if( 'payment_method' !== $key ){
            $price = intval(wp_strip_all_tags($total_rows[$key]['value']));
            if(!$price){
                $total_rows[$key]['value'] = '<span class="woocommerce-Price-amount amount">Liên hệ</span>';
            }
        }
    }
    return $total_rows;
}
