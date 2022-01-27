<?php
/**
 * Underscore js Template for adding customizer setting for WordPress Customizer Search.
 *
 * @since  1.0.0
 * @package  Customizer_Search
 */

?>

<script type="text/html" id="tmpl-search-button">

	<button type="button" class="customize-search-toggle dashicons dashicons-search" aria-expanded="false"><span class="screen-reader-text">Search</span></button>

</script>

<script type="text/html" id="tmpl-search-form">
	<div id="accordion-section-wp-customizer-search" style="display: none;">
		<h4 class="wp-customizer-search-section accordion-section-title">
			<span class="search-input"><?php _e( 'Search', 'wp-customizer-search' ); ?></span>
			<span class="search-field-wrapper">
				<input type="text" placeholder="<?php _e( 'Search...', 'wp-customizer-search' ); ?>" name="wp-customizer-search-input" autofocus="autofocus" id="wp-customizer-search-input" class="wp-customizer-search-input">
				<button type="button" class="button clear-search" tabindex="0"><?php _e( 'Clear', 'wp-customizer-search' ); ?></button>
			</span>

		</h4>
	</div>
</script>
