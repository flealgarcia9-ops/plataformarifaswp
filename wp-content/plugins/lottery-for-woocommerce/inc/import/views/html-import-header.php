<?php
/**
 * Import - Header.
 *
 * @since 9.9.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<header class='wc-backbone-modal-header'>
	<span class='lty-import-title'><h1><b><?php echo wp_kses_post($this->get_popup_header_label()); ?></b></h1></span>
</header>
<?php
