<?php
/**
 * Plugin Name: Rifas Custom
 * Description: Funcionalidad custom de Rifas Los Primos
 * Version: 1.0.0
 */
/**
 * Rifas Los Primos - Elementor Child Theme
 */

// ============================================================
//  CONFIGURACIÓN CENTRALIZADA
//  Se edita desde wp-admin → Rifas Config
// ============================================================

function rifas_get_default_config() {
    return [
        'precio_unitario'      => 7000,
        'precio_descuento'     => 6000,
        'umbral_descuento'     => 5,
        'cantidades_botones'   => [3, 4, 5, 15, 20, 50, 100],
        'botones_destacados'   => [5, 100],
        'total_boletas'        => 1000,
        'porcentaje_vendido'   => 34,
        'texto_barra_progreso' => 'Compra que se va volando!',
        'fecha_promo'          => '30 de mayo 2026',
        'descripcion_hero'     => 'Cada uno de nuestros stickers viene acompañados de una experiencia unica para nuestros clientes. Tu apoyo hace parte de algo especial.',
        'producto_id'          => 15,
    ];
}

function rifas_get_config() {
    $defaults = rifas_get_default_config();
    $saved    = get_option('rifas_config', []);
    return wp_parse_args($saved, $defaults);
}

if (!defined('RIFAS_CONFIG')) {
    define('RIFAS_CONFIG', rifas_get_config());
}

// Enqueue parent + child styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('hello-elementor', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('rifas-child', get_stylesheet_directory_uri() . '/style.css', ['hello-elementor'], '3.0.2');
});

// ===== Custom Logo Header =====
add_action('wp_body_open', function () {
    $logo_url = get_stylesheet_directory_uri() . '/logo-v3.png?v=' . time();
    $cart_total = WC()->cart ? WC()->cart->get_cart_total() : wc_price(0);
    ?>
    <header class="rifas-top-header">
        <div class="rifas-header-inner">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url($logo_url); ?>" alt="Los Primos Motors" class="rifas-header-logo-img" style="max-width:180px!important;height:auto!important;">
            </a>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="rifas-header-cart">
                Compra/<?php echo $cart_total; ?> 🛒
            </a>
        </div>
    </header>
    <?php
});

// ===== Hide default Hello Elementor header =====
add_action('wp_head', function () {
    echo '<style>.site-header { display: none !important; }</style>';
});

// ===== Hero Section Shortcode =====
add_shortcode('rifas_hero', function ($atts) {
    $cfg = RIFAS_CONFIG;
    $atts = shortcode_atts([
        'product_id'         => $cfg['producto_id'],
        'product_image'      => '',
        'title'              => '',
        'price'              => '',
        'discount_price'     => number_format($cfg['precio_descuento'], 0, ',', '.'),
        'discount_threshold' => $cfg['umbral_descuento'],
        'promo_date'         => $cfg['fecha_promo'],
        'description'        => $cfg['descripcion_hero'],
    ], $atts);

    // Get product data from WooCommerce
    $product = wc_get_product(intval($atts['product_id']));
    $product_image = $atts['product_image'] ?: ($product ? get_the_post_thumbnail_url($product->get_id(), 'large') : '');
    $product_name = $atts['title'] ?: ($product ? str_replace(' ', "\n", $product->get_name()) : "STICKER\nYAMAHA XTZ 150");
    $product_price = $atts['price'] ?: ($product ? number_format($product->get_regular_price(), 0, ',', '.') : number_format($cfg['precio_unitario'], 0, ',', '.'));

    ob_start();
    ?>
    <section class="rifas-hero">
        <?php if ($product_image) : ?>
            <div class="rifas-hero-image"><img src="<?php echo esc_url($product_image); ?>" alt="Premio"></div>
        <?php endif; ?>
        <div class="rifas-hero-info">
            <h1><?php echo nl2br(esc_html($product_name)); ?></h1>
            <div class="rifas-hero-price">Valor de sticker <span class="rifas-price-highlight">$<?php echo esc_html($product_price); ?></span> mil pesos</div>
            <?php if ($atts['discount_price'] && $atts['discount_threshold']) : ?>
            <div class="rifas-hero-discount">Si compras <?php echo esc_html($atts['discount_threshold']); ?> o mas te quedan en <span class="rifas-price-highlight">$<?php echo esc_html($atts['discount_price']); ?></span></div>
            <?php endif; ?>
            <div class="rifas-hero-description"><?php echo esc_html($atts['description']); ?></div>
        </div>
    </section>
    <?php
    return ob_get_clean();
});

// ===== Progress Bar Shortcode =====
add_shortcode('rifas_progress', function ($atts) {
    $cfg = RIFAS_CONFIG;
    $atts = shortcode_atts([
        'percentage'    => $cfg['porcentaje_vendido'],
        'total_tickets' => $cfg['total_boletas'],
        'label'         => $cfg['texto_barra_progreso'],
    ], $atts);
    $percentage = max(0, min(100, intval($atts['percentage'])));
    $total = intval($atts['total_tickets']);
    ob_start();
    ?>
    <div class="rifas-progress-section">
        <h2><?php echo esc_html($atts['label']); ?></h2>
        <div class="rifas-progress-bar"><div class="rifas-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div></div>
        <?php if ($total > 0) : ?>
        <p class="rifas-progress-text"><?php echo esc_html($percentage); ?>% de <?php echo number_format($total, 0, ',', '.'); ?> boletas vendidas</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
});

// ===== Quantity Grid Shortcode =====
add_shortcode('rifas_quantity_grid', function ($atts) {
    $cfg = RIFAS_CONFIG;
    $atts = shortcode_atts([
        'product_id'         => $cfg['producto_id'],
        'unit_price'         => $cfg['precio_unitario'],
        'discount_price'     => $cfg['precio_descuento'],
        'discount_threshold' => $cfg['umbral_descuento'],
        'quantities'         => implode(',', $cfg['cantidades_botones']),
        'highlight'          => implode(',', $cfg['botones_destacados']),
    ], $atts);
    $product_id = intval($atts['product_id']);
    // Get price from WooCommerce product if not explicitly set
    $product = wc_get_product($product_id);
    $unit_price = $atts['unit_price'] !== '' ? intval($atts['unit_price']) : ($product ? intval($product->get_regular_price()) : $cfg['precio_unitario']);
    $discount_price = intval($atts['discount_price']);
    $discount_threshold = intval($atts['discount_threshold']);
    $quantities = array_map('intval', explode(',', $atts['quantities']));
    $highlights = array_map('intval', explode(',', $atts['highlight']));
    ob_start();
    ?>
    <div class="rifas-quantity-grid">
        <?php foreach ($quantities as $qty) :
            $effective_price = ($qty >= $discount_threshold) ? $discount_price : $unit_price;
            $total = $qty * $effective_price;
            $is_highlight = in_array($qty, $highlights);
            $btn_class = $is_highlight ? 'rifas-qty-btn--red' : 'rifas-qty-btn--green';
            $url = add_query_arg(['add-to-cart' => $product_id, 'quantity' => $qty], wc_get_cart_url());
        ?>
            <a href="<?php echo esc_url($url); ?>" class="rifas-qty-btn <?php echo esc_attr($btn_class); ?>">
                COMPRAR <?php echo $qty; ?> STICKERS $<?php echo number_format($total, 0, ',', '.'); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="rifas-custom-qty">
        <label for="rifas-custom-qty-input">Cantidad:</label>
        <input type="number" id="rifas-custom-qty-input" value="3" min="1">
        <button type="button" class="rifas-buy-now-btn" onclick="var q=document.getElementById('rifas-custom-qty-input').value; if(q>0){ window.location='<?php echo esc_url(wc_get_cart_url()); ?>?add-to-cart=<?php echo esc_attr($product_id); ?>&quantity='+q; }">
            Comprar ahora
        </button>
    </div>
    <?php
    return ob_get_clean();
});

// ===== WhatsApp Banner Shortcode =====
add_shortcode('rifas_whatsapp_banner', function ($atts) {
    $atts = shortcode_atts([
        'text' => 'Si no cuentas con pagos electrónicos has click',
        'link_text' => 'AQUÍ', 'link_url' => 'https://wa.me/573123567371',
        'subtext' => 'y uno de nuestros asesores te ayudará con tu compra.',
        'button_text' => 'SI TIENES ALGUNA DUDA, AQUÍ TE AYUDAMOS',
    ], $atts);
    ob_start();
    ?>
    <div class="rifas-whatsapp-banner">
        <p><?php echo esc_html($atts['text']); ?> <a href="<?php echo esc_url($atts['link_url']); ?>" target="_blank"><?php echo esc_html($atts['link_text']); ?></a> <?php echo esc_html($atts['subtext']); ?></p>
        <a href="<?php echo esc_url($atts['link_url']); ?>" target="_blank" class="rifas-whatsapp-btn"><?php echo esc_html($atts['button_text']); ?> 💬</a>
    </div>
    <?php
    return ob_get_clean();
});

// ===== Footer Shortcode =====
add_shortcode('rifas_footer', function ($atts) {
    $atts = shortcode_atts(['phone' => '(+57) 3123567371', 'email' => 'LosPrimosMotors57@gmail.com', 'designer' => 'David Ruiz'], $atts);
    ob_start();
    ?>
    <footer class="rifas-footer">
        <div class="rifas-footer-grid">
            <div>
                <h3>Contáctanos</h3>
                <p>Dudas e inquietudes por favor comunicarse</p>
                <p><strong>Movil:</strong> <?php echo esc_html($atts['phone']); ?></p>
                <p><strong>Correo:</strong> <a href="mailto:<?php echo esc_attr($atts['email']); ?>"><?php echo esc_html($atts['email']); ?></a></p>
            </div>
            <div><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/logo-v3.png?v=' . time()); ?>" alt="Los Primos Motors" class="rifas-footer-logo"></div>
            <div><p>Diseño by <a href="#" style="color:var(--rifas-red);"><?php echo esc_html($atts['designer']); ?></a></p></div>
        </div>
    </footer>
    <?php
    return ob_get_clean();
});

// ===== Hide page title on homepage =====
add_filter('hello_elementor_page_title', function ($show_title) {
    return !is_front_page();
});

// ===== WhatsApp Float Button =====
add_action('wp_footer', function () {
    $url = 'https://wa.me/573123567371';
    ?>
    <a href="<?php echo esc_url($url); ?>" target="_blank" class="rifas-whatsapp-float" aria-label="WhatsApp">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    <?php
});

// ===== Redirect add-to-cart to cart page =====
add_filter('woocommerce_add_to_cart_redirect', function ($url) {
    return wc_get_cart_url();
});

// ===== Hide Unused Checkout Fields =====
add_filter('woocommerce_checkout_fields', function ($fields) {
    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_address_1']);
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_country']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    $fields['billing']['billing_first_name']['label'] = 'Nombre';
    $fields['billing']['billing_last_name']['label'] = 'Apellidos';
    $fields['billing']['billing_phone']['label'] = 'Teléfono';
    $fields['billing']['billing_email']['label'] = 'Dirección de correo electrónico';
    $fields['billing']['billing_city']['label'] = 'Localidad / Ciudad';
    return $fields;
});

// ===== Change Place Order Button Text =====
add_filter('woocommerce_order_button_text', function () {
    return 'Realizar pedido';
});

// ===== Checkout without Payment Gateway =====
add_filter('woocommerce_cart_needs_payment', '__return_false');
add_action('wp_head', function () {
    if (is_checkout()) {
        echo '<style>
            .wc_payment_methods, .payment_box, .payment_method,
            #payment .woocommerce-terms-and-conditions-wrapper,
            .woocommerce-privacy-policy-text,
            .woocommerce-checkout-payment .payment_methods,
            .woocommerce-notices-wrapper .woocommerce-error { display: none !important; }
            #payment { background: transparent !important; border: none !important; display: block !important; }
            #payment .place-order { padding: 0 !important; margin-top: 20px !important; display: block !important; }
            #place_order { width: 100% !important; max-width: 400px !important; margin: 0 auto !important; display: block !important; background: var(--rifas-green) !important; color: #fff !important; font-weight: 700 !important; font-size: 1rem !important; padding: 16px !important; border-radius: 8px !important; border: none !important; cursor: pointer !important; }
        </style>';
    }
});

// ===== Change cart button text =====
add_filter('woocommerce_product_single_add_to_cart_text', function () {
    return 'Comprar boletas';
});

// ===== Clear cart before adding raffle product =====
add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if ($product_id == 15) { WC()->cart->empty_cart(); }
    return $passed;
}, 5, 6);

// ===== Force no-cache on logo =====
add_action('wp_head', function () {
    echo '<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">';
    echo '<meta http-equiv="Pragma" content="no-cache">';
    echo '<meta http-equiv="Expires" content="0">';
}, 1);

// ===== Cart page shows normally =====
// User flow: Homepage -> Cart -> Checkout


// ===== MANUAL TICKET SELECTION IN CART (Option B) =====

// 1. Remove plugin's cart validation that removes products without tickets

// 2. Add our own validation: only require tickets at checkout, not in cart
add_action('woocommerce_check_cart_items', function () {
    if (!is_object(WC()->cart)) return;
    $cart_items = WC()->cart->get_cart();
    if (empty($cart_items)) return;
    
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $product = isset($cart_item['data']) ? $cart_item['data'] : false;
        if (!is_object($product) || 'lottery' != $product->get_type()) continue;
        if (!$product->is_manual_ticket()) continue;
        
        // On checkout, require tickets to be selected
        if (is_checkout()) {
            $tickets = isset($cart_item['lty_lottery']['tickets']) ? $cart_item['lty_lottery']['tickets'] : array();
            if (empty($tickets)) {
                wc_add_notice(sprintf(__('Por favor selecciona los números de tickets para "%s" en el carrito antes de continuar.', 'lottery-for-woocommerce'), $product->get_name()), 'error');
                wp_safe_redirect(wc_get_cart_url());
                exit;
            }
        }
    }
}, 10);

// 3. Show ticket selection form in cart for manual ticket products
add_action('woocommerce_after_cart_table', function () {
    if (!WC()->cart || WC()->cart->is_empty()) return;
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = isset($cart_item['data']) ? $cart_item['data'] : false;
        if (!is_object($product) || 'lottery' != $product->get_type()) continue;
        if (!$product->is_manual_ticket()) continue;
        
        $product_id = $product->get_id();
        $current_tickets = isset($cart_item['lty_lottery']['tickets']) ? $cart_item['lty_lottery']['tickets'] : array();
        $current_tickets_str = !empty($current_tickets) ? implode(',', $current_tickets) : '';
        $min_tickets = method_exists($product, 'get_lty_minimum_tickets') ? intval($product->get_lty_minimum_tickets()) : 1;
        $max_tickets = method_exists($product, 'get_lty_maximum_tickets') ? intval($product->get_lty_maximum_tickets()) : 0;
        
        // Get all ticket data
        $overall = method_exists($product, 'get_overall_tickets') ? $product->get_overall_tickets() : array();
        $placed = method_exists($product, 'get_placed_tickets') ? $product->get_placed_tickets() : array();
        $cart_tickets_all = method_exists($product, 'get_cart_tickets') ? $product->get_cart_tickets() : array();
        $reserved = method_exists($product, 'get_reserved_tickets') ? $product->get_reserved_tickets() : array();
        
        // Exclude current item's tickets from "other cart"
        $other_cart = array_diff($cart_tickets_all, $current_tickets);
        
        // Build ticket status map
        $ticket_status = array();
        foreach ($overall as $t) {
            if (in_array($t, $current_tickets)) {
                $ticket_status[$t] = 'selected';
            } elseif (in_array($t, $placed)) {
                $ticket_status[$t] = 'sold';
            } elseif (in_array($t, $other_cart)) {
                $ticket_status[$t] = 'in-cart';
            } elseif (in_array($t, $reserved)) {
                $ticket_status[$t] = 'reserved';
            } else {
                $ticket_status[$t] = 'available';
            }
        }
        
        // Pagination: 100 per tab
        $tabs = array();
        $total = count($overall);
        $tab_size = 100;
        for ($i = 0; $i < $total; $i += $tab_size) {
            $start = $i;
            $end = min($i + $tab_size - 1, $total - 1);
            $tabs[] = array('start' => $start, 'end' => $end, 'label' => ($start + 1) . '-' . ($end + 1));
        }
        
        $grid_id = 'rifas-grid-' . $product_id;
        ?>
        <div class="rifas-ticket-grid-wrapper">
            <h4>🎫 <?php echo esc_html($product->get_name()); ?> — Selecciona tus números</h4>
            
            <div class="rifas-ticket-toolbar">
                <input type="number" id="<?php echo $grid_id; ?>-qty" value="3" min="1" max="100" placeholder="Cant.">
                <button type="button" class="rifas-lucky-dip-btn" onclick="rifasLuckyDip('<?php echo $grid_id; ?>')">🎲 Lucky Dip</button>
                <input type="text" class="rifas-search-ticket-input" id="<?php echo $grid_id; ?>-search" placeholder="Buscar ticket...">
                <button type="button" class="rifas-search-ticket-btn" onclick="rifasSearchTicket('<?php echo $grid_id; ?>')">Buscar</button>
            </div>
            
            <?php if (!empty($tabs)) : ?>
            <div class="rifas-ticket-tabs">
                <?php foreach ($tabs as $idx => $tab) : ?>
                    <button type="button" class="rifas-ticket-tab <?php echo $idx === 0 ? 'active' : ''; ?>" 
                            onclick="rifasShowTab('<?php echo $grid_id; ?>', <?php echo $idx; ?>)"
                            data-tab="<?php echo $idx; ?>">
                        <?php echo esc_html($tab['label']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="rifas-ticket-grid" id="<?php echo $grid_id; ?>">
                <?php foreach ($overall as $idx => $ticket) : 
                    $status = $ticket_status[$ticket] ?? 'available';
                    $tab_idx = floor($idx / $tab_size);
                ?>
                    <div class="rifas-ticket-cell <?php echo esc_attr($status); ?>" 
                         data-ticket="<?php echo esc_attr($ticket); ?>"
                         data-tab="<?php echo $tab_idx; ?>"
                         <?php if ($status === 'available' || $status === 'selected') : ?>
                         onclick="rifasToggleTicket('<?php echo $grid_id; ?>', '<?php echo esc_attr($ticket); ?>', this)"
                         <?php endif; ?>>
                        <?php echo esc_html($ticket); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="rifas-ticket-info">
                <span><span class="dot" style="background:#fff;border-color:#28a745;"></span> Disponible</span>
                <span><span class="dot" style="background:#28a745;"></span> Seleccionado</span>
                <span><span class="dot" style="background:#f8d7da;"></span> Vendido</span>
                <span><span class="dot" style="background:#fff3cd;"></span> En otro carrito</span>
                <span><span class="dot" style="background:#d1ecf1;"></span> Reservado</span>
            </div>
            
            <div class="rifas-ticket-count" id="<?php echo $grid_id; ?>-count">
                Seleccionados: <strong><?php echo count($current_tickets); ?></strong> 
                <?php if ($min_tickets > 0) echo '(mín. ' . $min_tickets . ')'; ?>
                <?php if ($max_tickets > 0) echo '(máx. ' . $max_tickets . ')'; ?>
            </div>
            
            <form method="post" action="" class="rifas-ticket-hidden-form" id="<?php echo $grid_id; ?>-form">
                <?php wp_nonce_field('rifas_save_tickets', 'rifas_tickets_nonce'); ?>
                <input type="hidden" name="rifas_cart_item_key" value="<?php echo esc_attr($cart_item_key); ?>">
                <input type="hidden" name="rifas_product_id" value="<?php echo esc_attr($product_id); ?>">
                <input type="hidden" name="rifas_ticket_numbers" id="<?php echo $grid_id; ?>-input" value="<?php echo esc_attr($current_tickets_str); ?>">
                <button type="submit" name="rifas_save_tickets" class="rifas-save-tickets-btn">
                    💾 Guardar selección
                </button>
            </form>
        </div>
        
        <script>
        (function() {
            var gridId = '<?php echo $grid_id; ?>';
            var selected = <?php echo json_encode(array_values($current_tickets)); ?>;
            var maxTickets = <?php echo $max_tickets > 0 ? $max_tickets : 'null'; ?>;
            var minTickets = <?php echo $min_tickets; ?>;
            var allTickets = <?php echo json_encode(array_values($overall)); ?>;
            var soldTickets = <?php echo json_encode(array_values($placed)); ?>;
            var inCartTickets = <?php echo json_encode(array_values($other_cart)); ?>;
            var reservedTickets = <?php echo json_encode(array_values($reserved)); ?>;
            
            function updateCount() {
                var countEl = document.getElementById(gridId + '-count');
                if (countEl) {
                    var msg = 'Seleccionados: <strong>' + selected.length + '</strong>';
                    if (minTickets > 0) msg += ' (mín. ' + minTickets + ')';
                    if (maxTickets) msg += ' (máx. ' + maxTickets + ')';
                    countEl.innerHTML = msg;
                }
                var input = document.getElementById(gridId + '-input');
                if (input) input.value = selected.join(',');
            }
            
            function updateCells() {
                var cells = document.querySelectorAll('#' + gridId + ' .rifas-ticket-cell');
                cells.forEach(function(cell) {
                    var t = cell.getAttribute('data-ticket');
                    cell.classList.remove('selected');
                    if (selected.indexOf(t) !== -1 && !cell.classList.contains('sold') && !cell.classList.contains('in-cart') && !cell.classList.contains('reserved')) {
                        cell.classList.add('selected');
                    }
                });
                updateCount();
            }
            
            window.rifasToggleTicket = function(gid, ticket, el) {
                if (gid !== gridId) return;
                var idx = selected.indexOf(ticket);
                if (idx !== -1) {
                    selected.splice(idx, 1);
                    el.classList.remove('selected');
                } else {
                    if (maxTickets && selected.length >= maxTickets) {
                        alert('Máximo ' + maxTickets + ' tickets permitidos.');
                        return;
                    }
                    selected.push(ticket);
                    el.classList.add('selected');
                }
                updateCount();
            };
            
            window.rifasShowTab = function(gid, tabIdx) {
                if (gid !== gridId) return;
                var cells = document.querySelectorAll('#' + gridId + ' .rifas-ticket-cell');
                cells.forEach(function(cell) {
                    var t = parseInt(cell.getAttribute('data-tab'));
                    cell.style.display = (t === tabIdx) ? 'flex' : 'none';
                });
                var tabWrap = document.getElementById(gridId).parentNode.querySelector('.rifas-ticket-tabs');
                if (tabWrap) {
                    var tabs = tabWrap.querySelectorAll('.rifas-ticket-tab');
                    tabs.forEach(function(tab) {
                        var t = parseInt(tab.getAttribute('data-tab'));
                        tab.classList.toggle('active', t === tabIdx);
                    });
                }
            };
            
            window.rifasLuckyDip = function(gid) {
                if (gid !== gridId) return;
                var qtyInput = document.getElementById(gridId + '-qty');
                var qty = qtyInput ? parseInt(qtyInput.value) || 3 : 3;
                if (maxTickets && qty > maxTickets) qty = maxTickets;
                if (qty < minTickets) qty = minTickets;
                
                var available = allTickets.filter(function(t) {
                    return soldTickets.indexOf(t) === -1 && inCartTickets.indexOf(t) === -1 && reservedTickets.indexOf(t) === -1;
                });
                
                selected = [];
                for (var i = 0; i < qty && available.length > 0; i++) {
                    var r = Math.floor(Math.random() * available.length);
                    selected.push(available[r]);
                    available.splice(r, 1);
                }
                updateCells();
                
                // Show all tabs to see selection
                var cells = document.querySelectorAll('#' + gridId + ' .rifas-ticket-cell');
                cells.forEach(function(cell) {
                    var t = cell.getAttribute('data-ticket');
                    cell.style.display = (selected.indexOf(t) !== -1) ? 'flex' : 'none';
                });
            };
            
            window.rifasSearchTicket = function(gid) {
                if (gid !== gridId) return;
                var searchInput = document.getElementById(gridId + '-search');
                var term = searchInput ? searchInput.value.trim() : '';
                if (!term) return;
                
                var cells = document.querySelectorAll('#' + gridId + ' .rifas-ticket-cell');
                cells.forEach(function(cell) {
                    var t = cell.getAttribute('data-ticket');
                    if (t.indexOf(term) !== -1) {
                        cell.style.display = 'flex';
                    } else {
                        cell.style.display = 'none';
                    }
                });
            };
            
            // Initialize: show first tab
            document.addEventListener('DOMContentLoaded', function() {
                rifasShowTab(gridId, 0);
                updateCells();
            });
        })();
        </script>
        <?php
    }
}, 10);

// 4. Handle ticket selection form submission
add_action('wp', function () {
    if (!is_cart() || !isset($_POST['rifas_save_tickets'])) return;
    
    if (!wp_verify_nonce($_POST['rifas_tickets_nonce'] ?? '', 'rifas_save_tickets')) {
        wc_add_notice('Error de seguridad. Intenta de nuevo.', 'error');
        return;
    }
    
    $cart_item_key = sanitize_text_field($_POST['rifas_cart_item_key'] ?? '');
    $product_id = intval($_POST['rifas_product_id'] ?? 0);
    $ticket_input = sanitize_text_field($_POST['rifas_ticket_numbers'] ?? '');
    
    if (empty($cart_item_key) || empty($product_id)) {
        wc_add_notice('Error: Datos inválidos.', 'error');
        return;
    }
    
    $product = wc_get_product($product_id);
    if (!is_object($product) || 'lottery' != $product->get_type() || !$product->is_manual_ticket()) {
        wc_add_notice('Error: Producto no válido.', 'error');
        return;
    }
    
    if (empty($ticket_input)) {
        wc_add_notice('Por favor ingresa al menos un número de ticket.', 'error');
        return;
    }
    
    $selected_tickets = array_map('trim', explode(',', $ticket_input));
    $selected_tickets = array_filter($selected_tickets);
    $selected_tickets = array_values($selected_tickets);
    
    if (empty($selected_tickets)) {
        wc_add_notice('Por favor ingresa números de tickets válidos.', 'error');
        return;
    }
    
    // Get product ticket settings
    $overall_tickets = method_exists($product, 'get_overall_tickets') ? $product->get_overall_tickets() : array();
    $placed_tickets = method_exists($product, 'get_placed_tickets') ? $product->get_placed_tickets() : array();
    $cart_tickets_all = method_exists($product, 'get_cart_tickets') ? $product->get_cart_tickets() : array();
    $reserved_tickets = method_exists($product, 'get_reserved_tickets') ? $product->get_reserved_tickets() : array();
    $min_tickets = method_exists($product, 'get_lty_minimum_tickets') ? intval($product->get_lty_minimum_tickets()) : 1;
    $max_tickets = method_exists($product, 'get_lty_maximum_tickets') ? intval($product->get_lty_maximum_tickets()) : 0;
    
    // Validate minimum
    if (count($selected_tickets) < $min_tickets) {
        wc_add_notice(sprintf('Debes seleccionar al menos %d ticket(s).', $min_tickets), 'error');
        return;
    }
    
    // Validate maximum
    if ($max_tickets > 0 && count($selected_tickets) > $max_tickets) {
        wc_add_notice(sprintf('Puedes seleccionar máximo %d ticket(s).', $max_tickets), 'error');
        return;
    }
    
    // Validate each ticket exists in overall tickets
    $invalid_tickets = array_diff($selected_tickets, $overall_tickets);
    if (!empty($invalid_tickets)) {
        wc_add_notice(sprintf('Los siguientes tickets no existen: %s', implode(', ', $invalid_tickets)), 'error');
        return;
    }
    
    // Validate tickets are not already placed (sold)
    $sold_tickets = array_intersect($selected_tickets, $placed_tickets);
    if (!empty($sold_tickets)) {
        wc_add_notice(sprintf('Los siguientes tickets ya fueron comprados: %s', implode(', ', $sold_tickets)), 'error');
        return;
    }
    
    // Get current cart item's existing tickets
    $current_cart_item = WC()->cart->get_cart_item($cart_item_key);
    $current_tickets = isset($current_cart_item['lty_lottery']['tickets']) ? $current_cart_item['lty_lottery']['tickets'] : array();
    
    // Validate tickets are not in other carts
    $other_cart_tickets = array_diff($cart_tickets_all, $current_tickets);
    $taken_tickets = array_intersect($selected_tickets, $other_cart_tickets);
    if (!empty($taken_tickets)) {
        wc_add_notice(sprintf('Los siguientes tickets están en el carrito de otro usuario: %s', implode(', ', $taken_tickets)), 'error');
        return;
    }
    
    // Validate reserved tickets
    $taken_reserved = array_intersect($selected_tickets, $reserved_tickets);
    if (!empty($taken_reserved)) {
        wc_add_notice(sprintf('Los siguientes tickets están reservados: %s', implode(', ', $taken_reserved)), 'error');
        return;
    }
    
    // All validations passed - save tickets to cart
    WC()->cart->cart_contents[$cart_item_key]['lty_lottery']['tickets'] = $selected_tickets;
    
    // Update quantity to match ticket count
    $ticket_count = count($selected_tickets);
    WC()->cart->set_quantity($cart_item_key, $ticket_count);
    
    // Persist cart session
    WC()->cart->set_session();
    
    wc_add_notice(sprintf('✅ Tickets guardados: %s', implode(', ', $selected_tickets)), 'success');
    
    wp_safe_redirect(wc_get_cart_url());
    exit;
});

// Remove plugin's cart validation - must run AFTER plugin registers its hooks
add_action('init', function () {
    if (class_exists('LTY_Lottery_Cart')) {
        remove_action('woocommerce_check_cart_items', array('LTY_Lottery_Cart', 'check_cart_items'), 1);
    }
}, 999);

// ===== VISUAL TICKET GRID STYLES =====
add_action('wp_head', function () {
    if (!is_cart()) return;
    echo '<style>
    .rifas-ticket-grid-wrapper { margin: 20px 0; padding: 25px; background: #f8f9fa; border-radius: 12px; border: 1px solid #e0e0e0; }
    .rifas-ticket-grid-wrapper h4 { margin: 0 0 15px 0; color: #28a745; font-size: 18px; }
    .rifas-ticket-toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; }
    .rifas-ticket-toolbar input[type="number"] { width: 60px; padding: 6px 10px; border: 1px solid #ddd; border-radius: 6px; text-align: center; }
    .rifas-ticket-toolbar button { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; }
    .rifas-lucky-dip-btn { background: #6f42c1; color: #fff; }
    .rifas-lucky-dip-btn:hover { background: #5a32a3; }
    .rifas-search-ticket-input { flex: 1; min-width: 150px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; }
    .rifas-search-ticket-btn { background: #495057; color: #fff; }
    .rifas-ticket-tabs { display: flex; gap: 4px; margin-bottom: 0; flex-wrap: wrap; }
    .rifas-ticket-tab { padding: 8px 16px; background: #e9ecef; border: none; border-radius: 6px 6px 0 0; cursor: pointer; font-weight: 600; font-size: 13px; color: #495057; }
    .rifas-ticket-tab.active { background: #fff; color: #28a745; border-bottom: 2px solid #28a745; }
    .rifas-ticket-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 6px; max-height: 400px; overflow-y: auto; padding: 10px; background: #fff; border-radius: 0 8px 8px 8px; }
    .rifas-ticket-cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border: 2px solid #e0e0e0; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s; background: #fff; color: #333; user-select: none; }
    .rifas-ticket-cell:hover:not(.sold):not(.reserved):not(.in-cart) { border-color: #28a745; transform: scale(1.05); z-index: 1; }
    .rifas-ticket-cell.selected { background: #28a745 !important; color: #fff !important; border-color: #28a745 !important; }
    .rifas-ticket-cell.sold { background: #f8d7da; color: #721c24; border-color: #f5c6cb; cursor: not-allowed; text-decoration: line-through; opacity: 0.7; }
    .rifas-ticket-cell.in-cart { background: #fff3cd; color: #856404; border-color: #ffeaa7; cursor: not-allowed; }
    .rifas-ticket-cell.reserved { background: #d1ecf1; color: #0c5460; border-color: #bee5eb; cursor: not-allowed; }
    .rifas-ticket-info { display: flex; gap: 15px; margin-top: 15px; font-size: 12px; flex-wrap: wrap; }
    .rifas-ticket-info span { display: flex; align-items: center; gap: 5px; }
    .rifas-ticket-info .dot { width: 14px; height: 14px; border-radius: 3px; border: 1px solid #ddd; }
    .rifas-ticket-count { margin: 15px 0 10px; font-size: 14px; font-weight: 600; }
    .rifas-save-tickets-btn { padding: 12px 30px; background: #28a745; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; width: 100%; max-width: 300px; }
    .rifas-save-tickets-btn:hover { background: #218838; }
    .rifas-ticket-hidden-form { display: block; margin-top: 10px; }
    @media (max-width: 768px) { .rifas-ticket-grid { grid-template-columns: repeat(5, 1fr); } }
    </style>';
});


// ============================================================
//  PÁGINA DE ADMIN: Configuración Rifas
//  wp-admin → Rifas Config
// ============================================================

add_action('admin_menu', function () {
    add_menu_page(
        'Configuración Rifas',
        'Rifas Config',
        'manage_options',
        'rifas-config',
        'rifas_render_config_page',
        'dashicons-tickets-alt',
        30
    );
});

function rifas_render_config_page() {
    if (!current_user_can('manage_options')) return;

    $defaults = rifas_get_default_config();
    $cfg      = rifas_get_config();

    // Guardar
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rifas_save_config'])) {
        check_admin_referer('rifas_config_nonce');

        $new = [
            'precio_unitario'      => intval($_POST['precio_unitario'] ?? $defaults['precio_unitario']),
            'precio_descuento'     => intval($_POST['precio_descuento'] ?? $defaults['precio_descuento']),
            'umbral_descuento'     => intval($_POST['umbral_descuento'] ?? $defaults['umbral_descuento']),
            'cantidades_botones'   => array_map('intval', array_filter(explode(',', $_POST['cantidades_botones'] ?? ''))),
            'botones_destacados'   => array_map('intval', array_filter(explode(',', $_POST['botones_destacados'] ?? ''))),
            'total_boletas'        => intval($_POST['total_boletas'] ?? $defaults['total_boletas']),
            'porcentaje_vendido'   => intval($_POST['porcentaje_vendido'] ?? $defaults['porcentaje_vendido']),
            'texto_barra_progreso' => sanitize_text_field($_POST['texto_barra_progreso'] ?? $defaults['texto_barra_progreso']),
            'fecha_promo'          => sanitize_text_field($_POST['fecha_promo'] ?? $defaults['fecha_promo']),
            'descripcion_hero'     => sanitize_textarea_field($_POST['descripcion_hero'] ?? $defaults['descripcion_hero']),
            'producto_id'          => intval($_POST['producto_id'] ?? $defaults['producto_id']),
        ];

        update_option('rifas_config', $new);
        echo '<div class="notice notice-success"><p>✅ Configuración guardada correctamente.</p></div>';
        $cfg = $new;
    }
    ?>
    <div class="wrap">
        <h1>🎫 Configuración Rifas</h1>
        <p>Edita precios, botones, barra de progreso y textos de la página principal.</p>

        <form method="post" style="max-width: 700px;">
            <?php wp_nonce_field('rifas_config_nonce'); ?>

            <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px;">💰 Precios</h2>
            <table class="form-table">
                <tr>
                    <th><label for="precio_unitario">Precio unitario ($)</label></th>
                    <td><input type="number" name="precio_unitario" id="precio_unitario" value="<?php echo esc_attr($cfg['precio_unitario']); ?>" class="small-text"> <span class="description">Precio de 1 sticker sin descuento</span></td>
                </tr>
                <tr>
                    <th><label for="precio_descuento">Precio con descuento ($)</label></th>
                    <td><input type="number" name="precio_descuento" id="precio_descuento" value="<?php echo esc_attr($cfg['precio_descuento']); ?>" class="small-text"> <span class="description">Precio por sticker al comprar volumen</span></td>
                </tr>
                <tr>
                    <th><label for="umbral_descuento">Mínimo para descuento</label></th>
                    <td><input type="number" name="umbral_descuento" id="umbral_descuento" value="<?php echo esc_attr($cfg['umbral_descuento']); ?>" class="small-text"> <span class="description">A partir de cuántos stickers aplica el descuento</span></td>
                </tr>
            </table>

            <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px;">🎯 Botones de cantidad (homepage)</h2>
            <table class="form-table">
                <tr>
                    <th><label for="cantidades_botones">Cantidades mostradas</label></th>
                    <td><input type="text" name="cantidades_botones" id="cantidades_botones" value="<?php echo esc_attr(implode(',', $cfg['cantidades_botones'])); ?>" class="regular-text"> <span class="description">Separadas por coma. Ej: 3,4,5,15,20,50,100</span></td>
                </tr>
                <tr>
                    <th><label for="botones_destacados">Botones destacados (rojos)</label></th>
                    <td><input type="text" name="botones_destacados" id="botones_destacados" value="<?php echo esc_attr(implode(',', $cfg['botones_destacados'])); ?>" class="regular-text"> <span class="description">Cuáles se pintan de rojo. Ej: 5,100</span></td>
                </tr>
            </table>

            <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px;">📊 Barra de progreso</h2>
            <table class="form-table">
                <tr>
                    <th><label for="total_boletas">Total de boletas</label></th>
                    <td><input type="number" name="total_boletas" id="total_boletas" value="<?php echo esc_attr($cfg['total_boletas']); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="porcentaje_vendido">% vendido</label></th>
                    <td><input type="number" name="porcentaje_vendido" id="porcentaje_vendido" value="<?php echo esc_attr($cfg['porcentaje_vendido']); ?>" class="small-text" min="0" max="100"></td>
                </tr>
                <tr>
                    <th><label for="texto_barra_progreso">Título barra</label></th>
                    <td><input type="text" name="texto_barra_progreso" id="texto_barra_progreso" value="<?php echo esc_attr($cfg['texto_barra_progreso']); ?>" class="regular-text"></td>
                </tr>
            </table>

            <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px;">📝 Textos</h2>
            <table class="form-table">
                <tr>
                    <th><label for="fecha_promo">Fecha de la promo</label></th>
                    <td><input type="text" name="fecha_promo" id="fecha_promo" value="<?php echo esc_attr($cfg['fecha_promo']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="descripcion_hero">Descripción (Hero)</label></th>
                    <td><textarea name="descripcion_hero" id="descripcion_hero" rows="3" class="large-text"><?php echo esc_textarea($cfg['descripcion_hero']); ?></textarea></td>
                </tr>
            </table>

            <h2 style="border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-top: 30px;">🛒 Producto</h2>
            <table class="form-table">
                <tr>
                    <th><label for="producto_id">Producto de la rifa</label></th>
                    <td>
                        <select name="producto_id" id="producto_id" style="min-width: 320px;">
                            <?php
                            // Usar WP_Query porque wc_get_products no trae todos los estados
                            $query = new WP_Query([
                                'post_type'      => 'product',
                                'posts_per_page' => -1,
                                'post_status'    => ['publish','draft','pending','private','trash','auto-draft'],
                                'orderby'        => 'ID',
                                'order'          => 'ASC',
                            ]);
                            $status_labels = [
                                'publish'    => '🟢 Publicado',
                                'draft'      => '🟡 Borrador',
                                'pending'    => '🟠 Pendiente',
                                'private'    => '🔒 Privado',
                                'trash'      => '🔴 Papelera',
                                'auto-draft' => '⚪ Auto-borrador',
                            ];
                            foreach ($query->posts as $post) :
                                $p = wc_get_product($post->ID);
                                if (!$p) continue;
                                $selected = selected($cfg['producto_id'], $p->get_id(), false);
                                $status   = $p->get_status();
                                $label    = $status_labels[$status] ?? $status;
                                $type     = $p->get_type();
                            ?>
                                <option value="<?php echo esc_attr($p->get_id()); ?>" <?php echo $selected; ?>>
                                    #<?php echo esc_html($p->get_id()); ?> — <?php echo esc_html($p->get_name()); ?> [<?php echo esc_html($label); ?>] (<?php echo esc_html($type); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="description">Selecciona cualquier producto de WooCommerce. Los de tipo <strong>lottery</strong> funcionan con la cuadrícula de tickets.</span>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="rifas_save_config" class="button button-primary button-hero">💾 Guardar configuración</button>
            </p>
        </form>
    </div>
    <?php
}

// ============================================================
//  EMAIL: Mostrar tickets de lotería en emails de WooCommerce
// ============================================================
add_action('woocommerce_email_after_order_table', function ($order, $sent_to_admin, $plain_text, $email) {
    $has_tickets = false;
    $output = '';
    
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!is_object($product) || 'lottery' !== $product->get_type()) {
            continue;
        }
        
        $tickets = $item->get_meta('_lty_lottery_tickets');
        if (empty($tickets) || !is_array($tickets)) {
            continue;
        }
        
        if (!$has_tickets) {
            $output .= '<h2 style="color:#28a745;margin-top:20px;">🎫 Tus números de la rifa</h2>';
            $has_tickets = true;
        }
        
        $output .= '<div style="margin:10px 0;padding:12px;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:8px;">';
        $output .= '<strong>' . esc_html($product->get_name()) . '</strong><br>';
        $output .= '<span style="font-size:16px;color:#333;">' . esc_html(implode(', ', $tickets)) . '</span>';
        $output .= '</div>';
    }
    
    if ($has_tickets) {
        echo wp_kses_post($output);
    }
}, 10, 4);

// También mostrar en la página de confirmación del pedido (thank you page)
add_action('woocommerce_thankyou', function ($order_id) {
    $order = wc_get_order($order_id);
    if (!is_object($order)) return;
    
    $has_tickets = false;
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!is_object($product) || 'lottery' !== $product->get_type()) continue;
        
        $tickets = $item->get_meta('_lty_lottery_tickets');
        if (empty($tickets) || !is_array($tickets)) continue;
        
        if (!$has_tickets) {
            echo '<h3 style="color:#28a745;">🎫 Tus números de la rifa</h3>';
            $has_tickets = true;
        }
        
        echo '<div style="margin:10px 0;padding:12px;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:8px;">';
        echo '<strong>' . esc_html($product->get_name()) . '</strong><br>';
        echo '<span style="font-size:16px;">' . esc_html(implode(', ', $tickets)) . '</span>';
        echo '</div>';
    }
});

// Mostrar tickets en los detalles del pedido (Mi Cuenta)
add_action('woocommerce_order_item_meta_start', function ($item_id, $item, $order) {
    $product = $item->get_product();
    if (!is_object($product) || 'lottery' !== $product->get_type()) return;
    
    $tickets = $item->get_meta('_lty_lottery_tickets');
    if (!empty($tickets) && is_array($tickets)) {
        echo '<p style="margin:5px 0;"><strong>🎫 Números:</strong> ' . esc_html(implode(', ', $tickets)) . '</p>';
    }
}, 10, 3);
