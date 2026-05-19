<?php
/**
 * Pricing Tiers para Rifas
 * Usa el precio unitario y cantidades de la configuración central (Rifas Config)
 */

// ============================================================
//  PRICING TIERS - Precios por Cantidad (vinculado a Rifas Config)
// ============================================================

/**
 * Obtiene los pricing tiers guardados o genera defaults desde Rifas Config
 */
function rifas_get_pricing_tiers() {
    $cfg = rifas_get_config();
    $unit_price = intval($cfg['precio_unitario']);
    $quantities = array_map('intval', $cfg['cantidades_botones']);
    
    $saved = get_option('rifas_pricing_tiers', null);
    
    if ($saved !== null && is_array($saved)) {
        // Asegurar que los precios guardados usen el precio unitario actual
        foreach ($saved as $key => $tier) {
            $saved[$key]['unit_price'] = $unit_price;
        }
        return apply_filters('rifas_pricing_tiers', $saved);
    }
    
    // Generar defaults desde la config
    $defaults = [];
    foreach ($quantities as $qty) {
        $defaults[$qty] = [
            'qty' => $qty,
            'price' => $qty * $unit_price,  // Precio normal por defecto
            'label' => '',
            'highlight' => in_array($qty, $cfg['botones_destacados']),
        ];
    }
    
    return apply_filters('rifas_pricing_tiers', $defaults);
}

function rifas_save_pricing_tiers($tiers) {
    update_option('rifas_pricing_tiers', $tiers);
}

function rifas_calculate_price($quantity, $unit_price = null) {
    if ($unit_price === null) {
        $cfg = rifas_get_config();
        $unit_price = intval($cfg['precio_unitario']);
    }
    
    $tiers = rifas_get_pricing_tiers();
    if (isset($tiers[$quantity])) {
        return intval($tiers[$quantity]['price']);
    }
    
    return $quantity * $unit_price;
}

function rifas_get_tier_label($quantity) {
    $tiers = rifas_get_pricing_tiers();
    return isset($tiers[$quantity]) ? $tiers[$quantity]['label'] : '';
}

function rifas_is_tier_highlighted($quantity) {
    $tiers = rifas_get_pricing_tiers();
    return isset($tiers[$quantity]) ? !empty($tiers[$quantity]['highlight']) : false;
}

// ============================================================
//  ADMIN PAGE: Pricing Tiers
// ============================================================

add_action('admin_menu', function () {
    add_submenu_page(
        'rifas-config',
        'Precios por Cantidad',
        '💰 Precios',
        'manage_options',
        'rifas-pricing-tiers',
        'rifas_render_pricing_tiers_page'
    );
});

function rifas_render_pricing_tiers_page() {
    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos.');
    }

    $cfg = rifas_get_config();
    $unit_price = intval($cfg['precio_unitario']);
    $quantities = array_map('intval', $cfg['cantidades_botones']);
    
    // Guardar cambios
    if (isset($_POST['rifas_save_pricing']) && check_admin_referer('rifas_pricing_nonce')) {
        $tiers = [];
        foreach ($quantities as $qty) {
            $price = isset($_POST['price_' . $qty]) ? intval(str_replace(['.', ','], '', $_POST['price_' . $qty])) : ($qty * $unit_price);
            $label = isset($_POST['label_' . $qty]) ? sanitize_text_field($_POST['label_' . $qty]) : '';
            $highlight = isset($_POST['highlight_' . $qty]) ? true : false;
            
            $tiers[$qty] = [
                'qty' => $qty,
                'price' => $price,
                'label' => $label,
                'highlight' => $highlight,
            ];
        }
        rifas_save_pricing_tiers($tiers);
        echo '<div class="notice notice-success is-dismissible"><p>✅ Precios guardados correctamente.</p></div>';
    }
    
    // Resetear
    if (isset($_POST['rifas_reset_pricing']) && check_admin_referer('rifas_pricing_nonce')) {
        delete_option('rifas_pricing_tiers');
        echo '<div class="notice notice-warning is-dismissible"><p>⚠️ Precios restablecidos a valores por defecto.</p></div>';
    }
    
    $tiers = rifas_get_pricing_tiers();
    ?>
    <div class="wrap">
        <h1>💰 Precios por Cantidad</h1>
        
        <div class="notice notice-info">
            <p>
                <strong>Precio unitario base:</strong> $<?php echo number_format($unit_price, 0, ',', '.'); ?> 
                (configurado en <a href="<?php echo admin_url('admin.php?page=rifas-config'); ?>">Rifas Config</a>)
            </p>
            <p>
                <strong>Cantidades mostradas:</strong> <?php echo implode(', ', $quantities); ?> 
                (configurado en <a href="<?php echo admin_url('admin.php?page=rifas-config'); ?>">Rifas Config</a>)
            </p>
        </div>
        
        <p>Define el precio <strong>TOTAL</strong> que pagará el cliente por cada cantidad de stickers.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('rifas_pricing_nonce'); ?>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 100px;">Cantidad</th>
                        <th style="width: 150px;">Precio Total ($)</th>
                        <th style="width: 200px;">Etiqueta</th>
                        <th style="width: 80px;">Destacar</th>
                        <th style="width: 120px;">Precio Normal</th>
                        <th style="width: 120px;">Ahorro</th>
                        <th>Vista Previa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quantities as $qty) : 
                        $tier = isset($tiers[$qty]) ? $tiers[$qty] : ['qty' => $qty, 'price' => $qty * $unit_price, 'label' => '', 'highlight' => false];
                        $regular = $qty * $unit_price;
                        $savings = $regular - $tier['price'];
                        $savings_pct = $regular > 0 ? round(($savings / $regular) * 100) : 0;
                    ?>
                    <tr>
                        <td><strong><?php echo $qty; ?> sticker<?php echo $qty > 1 ? 's' : ''; ?></strong></td>
                        <td>
                            <input type="text" 
                                   name="price_<?php echo $qty; ?>" 
                                   value="<?php echo number_format($tier['price'], 0, ',', '.'); ?>"
                                   class="regular-text"
                                   style="width: 120px; text-align: right;"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
                        </td>
                        <td>
                            <input type="text" 
                                   name="label_<?php echo $qty; ?>" 
                                   value="<?php echo esc_attr($tier['label']); ?>"
                                   class="regular-text"
                                   style="width: 180px;"
                                   placeholder="Ej: MEJOR OFERTA">
                        </td>
                        <td style="text-align: center;">
                            <input type="checkbox" 
                                   name="highlight_<?php echo $qty; ?>" 
                                   value="1"
                                   <?php checked($tier['highlight']); ?>>
                        </td>
                        <td>$<?php echo number_format($regular, 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($savings > 0) : ?>
                                <span style="color: #d63638; font-weight: bold;">
                                    -$<?php echo number_format($savings, 0, ',', '.'); ?> 
                                    (<?php echo $savings_pct; ?>%)
                                </span>
                            <?php else : ?>
                                <span style="color: #999;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="
                                display: inline-block;
                                padding: 8px 16px;
                                border-radius: 6px;
                                font-weight: 600;
                                color: #fff;
                                <?php echo $tier['highlight'] ? 'background: linear-gradient(180deg, #ff4444 0%, #cc0000 100%);' : 'background: linear-gradient(180deg, #4ab863 0%, #3a9a50 100%);'; ?>
                            ">
                                <?php echo $qty; ?> STICKER<?php echo $qty > 1 ? 'S' : ''; ?> $<?php echo number_format($tier['price'], 0, ',', '.'); ?>
                                <?php if ($tier['label']) : ?>
                                    <span style="display: block; font-size: 10px; margin-top: 2px;"><?php echo esc_html($tier['label']); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="submit">
                <button type="submit" name="rifas_save_pricing" class="button button-primary button-hero">
                    💾 Guardar Precios
                </button>
                <button type="submit" name="rifas_reset_pricing" class="button button-secondary" 
                        onclick="return confirm('¿Restablecer precios por defecto?')">
                    🔄 Restablecer
                </button>
            </p>
        </form>
        
        <hr>
        <h2>📖 Instrucciones</h2>
        <ul style="list-style: disc; margin-left: 20px;">
            <li><strong>Precio Total:</strong> Valor completo que paga el cliente por esa cantidad.</li>
            <li><strong>Etiqueta:</strong> Texto sobre el botón (ej: "MEJOR OFERTA"). Vacío = sin etiqueta.</li>
            <li><strong>Destacar:</strong> Botón en <strong style="color: #d63638;">rojo</strong> con animación pulse.</li>
            <li><strong>Ahorro:</strong> Calculado automáticamente vs precio normal (cantidad × $<?php echo number_format($unit_price, 0, ',', '.'); ?>).</li>
        </ul>
        
        <div class="notice notice-warning">
            <p><strong>⚠️ Nota:</strong> Si cambias el "Precio unitario" o las "Cantidades mostradas" en <a href="<?php echo admin_url('admin.php?page=rifas-config'); ?>">Rifas Config</a>, debes venir aquí y ajustar los precios correspondientes.</p>
        </div>
    </div>
    <?php
}
