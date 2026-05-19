<?php
/**
 * Plugin Name: Rifas - Pago Nequi / Daviplata
 * Description: Pasarela de pago personalizada para Nequi y Daviplata via Wava
 * Version: 1.0.0
 * Author: Los Primos Motors
 */

if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', 'rifas_nequi_init_gateway');

function rifas_nequi_init_gateway() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_Gateway_Rifas_Nequi extends WC_Payment_Gateway {
        public function __construct() {
            $this->id                 = 'rifas_nequi';
            $this->icon               = '';
            $this->has_fields         = true;
            $this->method_title       = 'Nequi / Daviplata / Tarjeta';
            $this->method_description = 'Paga con Nequi, Daviplata o Tarjeta de crédito/débito';
            $this->supports           = ['products'];

            $this->init_form_fields();
            $this->init_settings();

            $this->title       = $this->get_option('title', 'Pago con Nequi / Daviplata / Tarjeta');
            $this->description = $this->get_option('description', 'Realiza tu pago de forma segura');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
            add_action('woocommerce_thankyou_' . $this->id, [$this, 'thankyou_page']);
            add_action('woocommerce_email_before_order_table', [$this, 'email_instructions'], 10, 3);
        }

        public function init_form_fields() {
            $this->form_fields = [
                'enabled' => [
                    'title'   => 'Habilitar',
                    'type'    => 'checkbox',
                    'label'   => 'Habilitar pago con Nequi / Daviplata',
                    'default' => 'yes',
                ],
                'title' => [
                    'title'       => 'Título',
                    'type'        => 'text',
                    'description' => 'Título que verá el cliente en el checkout',
                    'default'     => 'Pago con Nequi / Daviplata / Tarjeta',
                    'desc_tip'    => true,
                ],
                'description' => [
                    'title'       => 'Descripción',
                    'type'        => 'textarea',
                    'description' => 'Descripción que verá el cliente',
                    'default'     => 'Realiza tu pago de forma segura. Selecciona tu método preferido.',
                ],
                'nequi_number' => [
                    'title'       => 'Número Nequi',
                    'type'        => 'text',
                    'description' => 'Número de Nequi para recibir pagos',
                    'default'     => '3108831988',
                ],
                'wava_link' => [
                    'title'       => 'Link de Pago Wava',
                    'type'        => 'text',
                    'description' => 'URL del botón de pago Wava',
                    'default'     => '',
                ],
            ];
        }

        public function payment_fields() {
            $description = $this->get_description();
            if ($description) {
                echo wpautop(wptexturize($description));
            }
            ?>
            <div class="rifas-payment-box" style="background:#f9f9f9;border:1px solid #ddd;border-radius:8px;padding:16px;margin-top:12px;">
                <h4 style="margin:0 0 12px;font-size:1.05em;">Selecciona tu método de pago:</h4>
                <label style="display:flex;align-items:center;gap:10px;padding:10px;background:#fff;border-radius:6px;margin-bottom:8px;cursor:pointer;border:1px solid #e0e0e0;">
                    <input type="radio" name="rifas_payment_method" value="nequi" checked style="width:18px;height:18px;">
                    <span style="font-weight:600;">💜 Nequi</span>
                </label>
                <label style="display:flex;align-items:center;gap:10px;padding:10px;background:#fff;border-radius:6px;margin-bottom:8px;cursor:pointer;border:1px solid #e0e0e0;">
                    <input type="radio" name="rifas_payment_method" value="daviplata" style="width:18px;height:18px;">
                    <span style="font-weight:600;">❤️ Daviplata</span>
                </label>
                <label style="display:flex;align-items:center;gap:10px;padding:10px;background:#fff;border-radius:6px;cursor:pointer;border:1px solid #e0e0e0;">
                    <input type="radio" name="rifas_payment_method" value="tarjeta" style="width:18px;height:18px;">
                    <span style="font-weight:600;">💳 Tarjeta de crédito/débito</span>
                </label>
                <div id="rifas-nequi-info" style="margin-top:12px;padding:12px;background:#e8f5e9;border-radius:6px;font-size:0.95em;">
                    <p style="margin:0;"><strong>Instrucciones para Nequi:</strong></p>
                    <p style="margin:4px 0 0;">1. Abre tu app Nequi<br>2. Envía el pago al número <strong>3108831988</strong><br>3. Envía el comprobante por WhatsApp al <a href="https://wa.me/573108831988" target="_blank">+57 310 8831988</a></p>
                </div>
                <div id="rifas-daviplata-info" style="display:none;margin-top:12px;padding:12px;background:#ffebee;border-radius:6px;font-size:0.95em;">
                    <p style="margin:0;"><strong>Instrucciones para Daviplata:</strong></p>
                    <p style="margin:4px 0 0;">1. Abre tu app Daviplata<br>2. Envía el pago al número <strong>3108831988</strong><br>3. Envía el comprobante por WhatsApp al <a href="https://wa.me/573108831988" target="_blank">+57 310 8831988</a></p>
                </div>
                <div id="rifas-tarjeta-info" style="display:none;margin-top:12px;padding:12px;background:#e3f2fd;border-radius:6px;font-size:0.95em;">
                    <p style="margin:0;"><strong>Pago con Tarjeta:</strong></p>
                    <p style="margin:4px 0 0;">Serás redirigido a nuestra pasarela de pago segura con tarjeta.</p>
                </div>
            </div>
            <script>
            (function() {
                var radios = document.querySelectorAll('input[name="rifas_payment_method"]');
                radios.forEach(function(r) {
                    r.addEventListener('change', function() {
                        document.getElementById('rifas-nequi-info').style.display = 'none';
                        document.getElementById('rifas-daviplata-info').style.display = 'none';
                        document.getElementById('rifas-tarjeta-info').style.display = 'none';
                        document.getElementById('rifas-' + this.value + '-info').style.display = 'block';
                    });
                });
            })();
            </script>
            <?php
        }

        public function validate_fields() {
            if (empty($_POST['rifas_payment_method'])) {
                wc_add_notice('Por favor selecciona un método de pago.', 'error');
                return false;
            }
            return true;
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            $payment_method = isset($_POST['rifas_payment_method']) ? sanitize_text_field($_POST['rifas_payment_method']) : 'nequi';

            $order->update_meta_data('_rifas_payment_method', $payment_method);

            if ($payment_method === 'nequi') {
                $order->set_payment_method_title('Nequi');
            } elseif ($payment_method === 'daviplata') {
                $order->set_payment_method_title('Daviplata');
            } else {
                $order->set_payment_method_title('Tarjeta de crédito/débito');
            }

            $order->update_status('on-hold', 'Esperando confirmación de pago ' . ucfirst($payment_method));
            $order->save();

            WC()->cart->empty_cart();

            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            ];
        }

        public function thankyou_page($order_id) {
            $order = wc_get_order($order_id);
            if (!$order) return;

            $payment_method = $order->get_meta('_rifas_payment_method');
            $total = $order->get_total();
            ?>
            <div style="background:#f5f5f5;border:1px solid #ddd;border-radius:8px;padding:20px;margin:20px 0;text-align:center;">
                <h3 style="color:#00c853;margin-top:0;">¡Gracias por tu compra!</h3>
                <p style="font-size:1.1em;">Tu pedido #<?php echo esc_html($order->get_order_number()); ?> está en espera de pago.</p>
                <div style="background:#fff;border-radius:6px;padding:16px;margin:16px 0;">
                    <p><strong>Total a pagar:</strong> <span style="font-size:1.3em;color:#00c853;"><?php echo wc_price($total); ?></span></p>
                    <?php if ($payment_method === 'nequi' || $payment_method === 'daviplata') : ?>
                        <p style="margin:8px 0;"><strong>Método:</strong> <?php echo esc_html(ucfirst($payment_method)); ?></p>
                        <p style="margin:8px 0;font-size:1.1em;"><strong>Número:</strong> <span style="color:#d32f2f;font-size:1.2em;">3108831988</span></p>
                        <p style="margin-top:16px;">
                            <a href="https://wa.me/573108831988?text=Hola!%20Acabo%20de%20realizar%20un%20pedido%20(%23<?php echo esc_attr($order->get_order_number()); ?>)%20por%20<?php echo urlencode(wc_price($total)); ?>%20por%20<?php echo esc_attr($payment_method); ?>.%20Adjunto%20mi%20comprobante." 
                               target="_blank" 
                               style="display:inline-block;background:#25d366;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600;">
                                📱 Enviar comprobante por WhatsApp
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }

        public function email_instructions($order, $sent_to_admin, $plain_text = false) {
            if ($order->get_payment_method() !== $this->id || $sent_to_admin) return;

            $payment_method = $order->get_meta('_rifas_payment_method');
            $total = $order->get_total();
            ?>
            <h2>Instrucciones de pago</h2>
            <p><strong>Total a pagar:</strong> <?php echo wc_price($total); ?></p>
            <p><strong>Método:</strong> <?php echo esc_html(ucfirst($payment_method)); ?></p>
            <p><strong>Número:</strong> 3108831988</p>
            <p>Una vez realizado el pago, envía el comprobante por WhatsApp al +57 310 8831988</p>
            <?php
        }
    }

    add_filter('woocommerce_payment_gateways', function ($gateways) {
        $gateways[] = 'WC_Gateway_Rifas_Nequi';
        return $gateways;
    });
}

// CSS adicional para el checkout
add_action('wp_enqueue_scripts', function () {
    if (!is_checkout()) return;
    wp_add_inline_style('woocommerce-inline', '
        .rifas-payment-box input[type="radio"] { accent-color: #00c853; }
        .rifas-payment-box label:hover { border-color: #00c853 !important; }
    ');
});
