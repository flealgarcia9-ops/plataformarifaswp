<?php
/**
 * Export - Header.
 *
 * @since 10.3.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<header class='wc-backbone-modal-header'>
	<span class='lty-export-title'><h1><b><?php echo wp_kses_post($this->get_popup_header_label()); ?></b></h1></span>
</header>
<?php
