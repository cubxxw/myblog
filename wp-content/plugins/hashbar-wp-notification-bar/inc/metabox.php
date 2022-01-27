<?php
add_action('admin_footer', function(){
    ?>
    <div style="display:none;">
        <span id="positioning_help_text">
            <b><?php echo esc_html__('Top', 'hashbar') ?></b> = <?php echo esc_html__('Notification bar will be displayed on top of the page.', 'hashbar') ?><br>
            <b><?php echo esc_html__('Bottom', 'hashbar') ?></b> = <?php echo esc_html__('Notification bar will be displayed on bottom of the page.', 'hashbar') ?><br>
            <b><?php echo esc_html__('Left Wall', 'hashbar') ?></b> = <?php echo esc_html__('Notification bar will be displayed on left wall of the page.', 'hashbar') ?> <br>
            <b><?php echo esc_html__('Right Wall', 'hashbar') ?></b> = <?php echo esc_html__('Notification bar will be displayed on right wall of the page.', 'hashbar') ?> <br>
            <b><?php echo esc_html__('Promo Banner', 'hashbar') ?></b> = <?php echo esc_html__('A floating banner like notification bar will be displayed on top/bottom of the page.', 'hashbar') ?> <br>
            <?php echo esc_html__('You may use the "Hashbar Promo Banner" blocks from the Gutenberg block editor for the "Promo Banner" position.', 'hashbar') ?>
        </span>
    </div>

    <div style="display:none;">
        <span id="themes_header_type_help_text">
            <?php echo esc_html__('Select what type of header you are using in your theme. ', 'hashbar') ?><br>
			<?php echo esc_html__('If it is transparent or sticky, make sure to specify the CSS selector of your header.', 'hashbar') ?>
        </span>
    </div>
    
	<script>
	   
	</script>
    <?php
});

if( !function_exists('hashbar_custom_date_field_callback') ){
	function hashbar_custom_date_field_callback( $args ){
		$field = $args['field_name']; 
		$post_id     = get_the_id();
		$field_value = get_post_meta($post_id, $field, true);
		?>
		<input type="text" name="_wphash_[<?php echo $field; ?>]" value="<?php echo esc_attr($field_value); ?>" data-depend-id="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($post_id).esc_attr($field); ?>">
		<div class="clear"></div>

		<?php if( isset($args['desc']) ): ?>
			<div class="csf-desc-text"><?php echo wp_kses_post($args['desc']) ?></div>
		<?php endif; ?>

		<div class="clear"></div>

		<script>
			;( function ( $ ) {
			    'use strict';

			    $(document).ready(function(){
	    		    $( "#<?php echo $post_id.$field; ?>" ).datetimepicker({
	    		    	controlType: 'select',
	    	    		oneLine: true,
	    	    		dateFormat: 'mm/dd/yy',
	    	    		timeFormat: 'hh:mm tt',
	    		    	beforeShow: function(input, inst){
	    		    		jQuery(inst.dpDiv).addClass('csf-datepicker-wrapper');
	    		    	},
	    		    });
			    });
			} )( jQuery );
		</script>
		<?php
	}
}


if( !function_exists('hashbar_spacing_field_callback') ){
	function hashbar_spacing_field_callback($args){
		$post_id   = get_the_id();
		$field_id  = $args['id'];
		$post_meta = get_post_meta($post_id, $field_id, true);

		$top_val    = isset($post_meta[$args['for'] . '_top']) ? $post_meta[$args['for'] . '_top'] : '';
		$right_val  = isset($post_meta[$args['for'] . '_right']) ? $post_meta[$args['for'] . '_right'] : '';
		$bottom_val = isset($post_meta[$args['for'] . '_bottom']) ? $post_meta[$args['for'] . '_bottom'] : '';
		$left_val   = isset($post_meta[$args['for'] . '_left']) ? $post_meta[$args['for'] . '_left'] : '';
		?>
		<div class="csf--inputs">
			<div class="csf--input">
				<span class="csf--label csf--icon"><?php echo esc_html__('Top', 'hashbar') ?></span>
				<input type="text" name="_wphash_[<?php echo esc_attr($field_id) ?>][<?php echo esc_attr($args['for']) ?>_top]" value="<?php echo esc_attr($top_val); ?>" placeholder="" class="csf-input-number">
			</div>
			<div class="csf--input">
				<span class="csf--label csf--icon"><?php echo esc_html__('Right', 'hashbar') ?></span>
				<input type="text" name="_wphash_[<?php echo esc_attr($field_id) ?>][<?php echo esc_attr($args['for']) ?>_right]" value="<?php echo esc_attr($right_val); ?>" placeholder="" class="csf-input-number">
			</div>
			<div class="csf--input">
				<span class="csf--label csf--icon"><?php echo esc_html__('Bottom', 'hashbar') ?></span>
				<input type="text" name="_wphash_[<?php echo esc_attr($field_id) ?>][<?php echo esc_attr($args['for']) ?>_bottom]" value="<?php echo esc_attr($bottom_val); ?>" placeholder="" class="csf-input-number">
			</div>
			<div class="csf--input">
				<span class="csf--label csf--icon"><?php echo esc_html__('Left', 'hashbar') ?></span>
				<input type="text" name="_wphash_[<?php echo esc_attr($field_id) ?>][<?php echo esc_attr($args['for']) ?>_left]" value="<?php echo esc_attr($left_val); ?>" placeholder="" class="csf-input-number">
			</div>
		</div>
		<div class="clear"></div>

		<?php if( isset($args['desc']) ): ?>
			<div class="csf-desc-text"><?php echo wp_kses_post($args['desc']) ?></div>
		<?php endif; ?>

		<?php
	}
}

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  	// Set a unique slug-like ID
	$prefix = '_wphash_';

	// Prepare where to show options array
	$where_to_show_options = array(
		'none'       => esc_html__( 'Don\'t show', 'hashbar' ),
		'everywhere' => esc_html__( 'Entire Site', 'hashbar' ),
		'homepage'   => esc_html__( 'Homepage Only', 'hashbar' ),
	);

	$custom_posts = array(
		'post' => esc_html__( 'Posts', 'hashbar' ),
		'page' => esc_html__( 'Pages', 'hashbar' ),
	);
	
	if( is_plugin_active('woocommerce/woocommerce.php') ){
		$custom_posts['product']                 = esc_html__( 'Products', 'hashbar' );
		$where_to_show_options                   = array_merge($where_to_show_options, $custom_posts);
		$where_to_show_options['woo_catagories'] = esc_html__( 'Products Of Selected Categories','hashbar' );
	}else{
		$where_to_show_options = array_merge($where_to_show_options, $custom_posts);
	}

	$where_to_show_options['specific_ids'] = esc_html__( 'Any Post/Page/Custom Post IDS', 'hashbar' );
	$where_to_show_options['url_param']    = esc_html__( 'URL Parameter', 'hashbar' );
	$where_to_show_options['custom']       = esc_html__( 'Custom', 'hashbar' );

	// Limit
	$hashbar_options = get_option( 'hashbar_wpnb_opt');

	// Defaults
	$limit = array(
		'post'    => 150,
		'page'    => 150,
		'product' => 150,
	);

	//set position options in classic and gutenberg editor 
	if(!hashbar_wpnb_is_classic_editor_plugin_active()){
		$wphash_positions = array(
			'ht-n-top'         => esc_html__( 'Top', 'hashbar' ),
			'ht-n-bottom'      => esc_html__( 'Bottom', 'hashbar' ),
			'ht-n-left'        => esc_html__( 'Left Wall', 'hashbar' ),
			'ht-n-right'       => esc_html__( 'Right Wall', 'hashbar' ),
			'ht-n_toppromo'    => esc_html__( 'Promo Banner Top', 'hashbar' ),
			'ht-n_bottompromo' => esc_html__( 'Promo Banner Bottom', 'hashbar' ),
		);
	}else{
		$wphash_positions = array(
			'ht-n-top'         => esc_html__( 'Top', 'hashbar' ),
			'ht-n-bottom'      => esc_html__( 'Bottom', 'hashbar' ),
			'ht-n-left'        => esc_html__( 'Left Wall', 'hashbar' ),
			'ht-n-right'       => esc_html__( 'Right Wall', 'hashbar' ),
		);
	}

	if( isset($hashbar_options['posts_limit']) && $hashbar_options['posts_limit'] ){
		$limit['post'] = $hashbar_options['posts_limit'];
	}

	if( isset($hashbar_options['pages_limit']) && $hashbar_options['pages_limit'] ){
		$limit['page'] = $hashbar_options['pages_limit'];
	}

	if( isset($hashbar_options['product_limit']) && $hashbar_options['product_limit'] ){
		$limit['product'] = $hashbar_options['product_limit'];
	}

	$enable_ajax_select = apply_filters( 'hashbar_ajax_for_select_fields', false );

	$section_1_fields = array();

	// Choose where to show
	$section_1_fields[] = array(
		'id'      => $prefix. 'notification_where_to_show',
		'type'    => 'select',
		'title'   => esc_html__( 'Where to show', 'hashbar' ),
		'desc'    => esc_html__( 'Choose the area of your site, where the notification should be displayed.', 'hashbar' ),
		'options' => $where_to_show_options,
		'default' => 'everywhere',
		'inline'  => true
	);

	// Select posts
	$section_1_fields[] = array(
		'id'          => $prefix. 'notification_where_to_show_Post',
		'type'        => 'select',
		'title'       => esc_html__( 'Choose posts', 'hashbar' ),
		'placeholder' => esc_html__('Select Posts'),
		'options'     => 'posts',
		'multiple'    => true,
		'chosen'      => true,
		'ajax'        => $enable_ajax_select,
		'query_args'  => array(
			'posts_per_page' => $enable_ajax_select ? '-1' : $limit['post']
		),
		'dependency'  => array( $prefix. 'notification_where_to_show', '==', 'post' ),
	);

	// Select pages
	$section_1_fields[] = array(
		'id'          => $prefix. 'notification_where_to_show_Page',
		'type'        => 'select',
		'title'       => esc_html__( 'Choose pages', 'hashbar' ),
		'placeholder' => esc_html__('Select Pages'),
		'options'     => 'pages',
		'multiple'    => true,
		'chosen'      => true,
		'ajax'        => $enable_ajax_select,
		'query_args'  => array(
			'posts_per_page' => $enable_ajax_select ? '-1' : $limit['page']
		),
		'dependency'  => array( $prefix. 'notification_where_to_show', '==', 'page' ),
	);

	// Select products
	if( isset($custom_posts['product']) ){
    	$section_1_fields[] = array(
			'id'          => $prefix. 'notification_where_to_show_Product',
			'type'        => 'select',
			'title'       => esc_html__( 'Choose products', 'hashbar' ),
			'placeholder' => esc_html__('Select Products'),
			'options'     => 'posts',
			'multiple'    => true,
			'chosen'      => true,
			'ajax'        => $enable_ajax_select,
			'query_args'  => array(
				'post_type'      => 'product',
				'posts_per_page' => $enable_ajax_select ? '-1' : $limit['product']
			),
			'dependency'  => array( $prefix. 'notification_where_to_show', '==', 'product' ),
    	);
	}

	if( is_plugin_active( 'woocommerce/woocommerce.php') ){
    	$section_1_fields[] = array(
			'id'         => $prefix. 'woocommerce_categories',
			'type'       => 'select',
			'title'      => esc_html__( 'Product categories', 'hashbar' ),
			'desc'       => esc_html__('This notification will appear into all the product details / archive pages of the selected categories above.', 'hashbar'),
			'placeholder'       => esc_html__('Select Products', 'hashbar'),
			'options'    => 'categories',
			'chosen'	 => true,
			'multiple'   => true,
			'query_args' => array(
				'taxonomy' => 'product_cat',
			),
			'dependency'  => array( $prefix. 'notification_where_to_show', '==', 'woo_catagories' ),
			'class' 	  => 'hashbar_pro_notice',
    	);
    	$section_1_fields[] = array(
			'id'         => $prefix. 'woocommerce_categories_archive_optin',
			'type'       => 'checkbox',
			'title'      => esc_html__( ' ', 'hashbar' ),
			'label'      => esc_html__( 'Disable for Archives', 'hashbar' ),
			'desc'       => esc_html__( 'By Checking ths box, this notification will not be displayed in the selected categorie(archive) page above.', 'hashbar' ),
			'dependency' => array( $prefix. 'notification_where_to_show|'. $prefix. 'woocommerce_categories', '==|!=', 'woo_catagories|' ),
			'class' 	  => 'hashbar_pro_opacity',
    	);
	}

	$section_1_fields[] = array(
		'id'         => $prefix. 'exclusion_page_for_notification',
		'type'       => 'text',
		'title'      => esc_html__( 'Exclude pages for notification', 'hashbar' ),
		'desc'       => esc_html__('Write any Page/Post/Custom Post ids here separated by comma. Example: 4,32,17.', 'hashbar'),
		'class' 	 => 'hashbar_pro_notice',
		'dependency' => array( $prefix. 'notification_where_to_show', '==', 'everywhere' ),
	);

	$section_1_fields[] = array(
		'id'         => $prefix. 'specific_post_ids',
		'type'       => 'text',
		'title'      => esc_html__( 'Post/Page/Custom post ids', 'hashbar' ),
		'desc'       => esc_html__('Put the post/page/custom post ids here separated by a comma. Example: 50,60,54', 'hashbar'),
		'dependency' => array( $prefix. 'notification_where_to_show', '==', 'specific_ids' ),
	);

	$section_1_fields[] = array(
		'id'         => $prefix. 'url_param',
		'type'       => 'text',
		'title'      => esc_html__( 'URL paramenter value', 'hashbar' ),
		'desc'       => esc_html__('Input URL parameter value, Example: discount_50 . So your URL should look like: example.com/?param=discount_50 . When visitors visit this URL they will see this notification.', 'hashbar'),
		'dependency' => array( $prefix. 'notification_where_to_show', '==', 'url_param' ),
	);

	$where_to_show_custom_options = array(
	    'home'  => esc_html__( 'Homepage', 'hashbar' ),
	    'posts' => esc_html__( 'All Posts', 'hashbar' ),
	    'page'  => esc_html__( 'All Pages', 'hashbar' ),
	);

	if( is_plugin_active('woocommerce/woocommerce.php') ){
		$where_to_show_custom_options['products'] = esc_html__( 'All Products', 'hashbar' );
	}

	$section_1_fields[] = array(
		'id'         => $prefix. 'notification_where_to_show_custom',
		'type'       => 'checkbox',
		'title'      => esc_html__( 'Custom options where to show', 'hashbar' ),
		'options'    => $where_to_show_custom_options,
		'dependency' => array( $prefix. 'notification_where_to_show', '==', 'custom' ),
	);


	// titles with help text
	$position_title_with_help_text = sprintf(
	    /*
	     * translators:
	     * 1: label
	     */
	    '%1$s <span class="tooltip" data-tooltip-content="#positioning_help_text"><i class="dashicons-before dashicons-editor-help"></i></span>',
	    esc_html__( 'Positioning', 'whols' )
	);
	$section_1_fields[] = array(
		'id'      => $prefix. 'notification_position',
		'type'    => 'radio',
		'title'   => $position_title_with_help_text,
		'options' => $wphash_positions,
		'inline'  => true,
		'default' => 'ht-n-top',
	);

	$themes_header_type_title = sprintf(
	    /*
	     * translators:
	     * 1: label
	     */
	    '%1$s <span class="tooltip" data-tooltip-content="#themes_header_type_help_text"><i class="dashicons-before dashicons-editor-help"></i></span>',
	    esc_html__( 'Theme\'s header type', 'whols' )
	);
	$section_1_fields[] = array(
		'id'      => $prefix. 'themes_header_type',
		'type'    => 'radio',
		'title'   => $themes_header_type_title,
		'options'            => array(
		    'none'          =>  esc_html__('Default', 'hashbar'),
		    'transparent'   =>  esc_html__('Transparent / Sticky', 'hashbar'),
		),
		'desc'       => __( 'Select what type of header you are using in your theme.<br>If it is transparent or sticky, make sure to specify the CSS selector of your header.', 'hashbar' ),
		'inline'     => true,
		'default'    => 'none',
		'dependency' => array($prefix. 'notification_position', '==', 'ht-n-top'),
		'class'		 => 'hashbar_pro_notice'		
	);

	$section_1_fields[] = array(
		'id'         => $prefix. 'notification_transparent_selector',
		'type'       => 'text',
		'title'      => esc_html__( 'Header CSS selector', 'hashbar' ),
		'desc'       => __( 'Input the CSS selector of your transparent / sticky header. Example: #header/.header', 'hashbar' ),
		'dependency' => array($prefix. 'notification_position|' . $prefix. 'themes_header_type', '==|==', 'ht-n-top|transparent'),
	);

	$section_1_fields[] = array(
		'id'      => $prefix. 'promo_banner_top_display',
		'type'    => 'select',
		'title'   => esc_html__( 'Promo banner top position', 'hashbar' ),
		'options' => array(
			'promo-top-left'  => esc_html__( 'Top Left', 'hashbar' ),
			'promo-top-right' => esc_html__( 'Top Rignt', 'hashbar' ),
		),
		'default' => 'promo-top-left',
		'dependency' => array($prefix. 'notification_position', '==', 'ht-n_toppromo'),
	);

	$section_1_fields[] = array(
		'id'      => $prefix. 'promo_banner_bottom_display',
		'type'    => 'select',
		'title'   => esc_html__( 'Promo banner bottom position', 'hashbar' ),
		'options' => array(
			'promo-bottom-left'  => esc_html__( 'Bottom Left', 'hashbar' ),
			'promo-bottom-right' => esc_html__( 'Bottom Right', 'hashbar' ),
		),
		'default' => 'promo-bottom-left',
		'dependency' => array($prefix. 'notification_position', '==', 'ht-n_bottompromo'),
	);

	$section_1_fields[] = array(
		'id'         => $prefix. 'notification_width',
		'type'       => 'text',
		'title'      => esc_html__( 'Notification area width', 'hashbar' ),
		'desc'       => esc_html__( 'Leave it empty for default. Example: 350px. It can be used for the Left/Right notification & promo banner.', 'hashbar' ),
	);

	$section_1_fields[] = array(
		'id'      => $prefix. 'notification_content_width',
		'type'    => 'select',
		'title'   => esc_html__( 'Content width', 'hashbar' ),
		'options' => array(
		    'default-width'  => esc_html__( 'Default', 'hashbar' ),
		    'ht-n-full-width'=> esc_html__( 'Full Width', 'hashbar' ),
		),
		'default'            => 'default-width',
		'dependency' => array($prefix. 'notification_position', 'any', 'ht-n-top,ht-n-bottom'),
	);


	$section_1_fields[] = array(
		'id'      => $prefix. 'notification_display',
		'type'    => 'select',
		'title'   => esc_html__( 'Load as minimized', 'hashbar' ),
		'desc'    => esc_html__( 'An arrow icon will be displayed instead of showing the notification. Clicking on the arrow button, the notification will be displayed.', 'hashbar' ),
		'options' => array(
			'ht-n-close' => esc_html__( 'Yes', 'hashbar' ),
			'ht-n-open'  => esc_html__( 'No', 'hashbar' ),
		),
		'default'	=> 'ht-n-open',
		'dependency' => array($prefix. 'notification_position', 'not-any', 'ht-n_toppromo,ht-n_bottompromo'),
	);

	$section_1_fields[] = array(
		'id'      => $prefix. 'notification_close_button',
		'type'    => 'select',
		'title'   => esc_html__( 'Show close button', 'hashbar' ),
		'desc'    => esc_html__( 'Control visibility of the close icon/button notification.', 'hashbar' ),
		'options' => array(
			'on'  => esc_html__( 'Yes', 'hashbar' ),
			'off' => esc_html__( 'No', 'hashbar' ),
		),
		'default'	=> 'on',
		// 'dependency' => array($prefix. 'notification_position', 'not-any', 'ht-n_toppromo,ht-n_bottompromo'),
	);
	
	$section_1_fields[] = array(
		'id'      => $prefix. 'hide_open_toggle',
		'type'    => 'select',
		'title'   => esc_html__( 'Show open button', 'hashbar' ),
		'desc'    => esc_html__( 'Control visibility of the open arrow icon/button.', 'hashbar' ),
		'options' => array(
		    'ntf_open_toggle_enable' => esc_html__( 'Yes', 'hashbar' ),
		    'ntf_open_toggle_disable'=> esc_html__( 'No', 'hashbar' ),
		),
		'default' => 'ntf_open_toggle_disable',
	);
	$section_1_fields[] = array(
		'id'         => $prefix. 'notification_close_button_text',
		'type'       => 'text',
		'title'      => esc_html__( 'Close button text', 'hashbar' ),
		'desc'       => esc_html__( 'Leave it empty for the default icon.', 'hashbar' ),
		'dependency' => array($prefix. 'notification_close_button', '==', 'on'),
	);

	$section_1_fields[] = array(
		'id'         => $prefix. 'notification_open_button_text',
		'type'       => 'text',
		'title'      => esc_html__( 'Open button text', 'hashbar' ),
		'desc'       => esc_html__( 'Leave it empty for the default icon.', 'hashbar' ),
		'dependency' => array($prefix. 'notification_position', 'not-any', 'ht-n_toppromo,ht-n_bottompromo'),
	);

	// Create a metabox
	CSF::createMetabox( $prefix, array(
		'title'     => esc_html__( 'Notification Bar Options', 'hashbar'),
		'post_type' => 'wphash_ntf_bar',
		'data_type' => 'unserialize',
		'context'   => 'normal',
		'theme'     => 'light',
	));

	// General Options
	CSF::createSection( $prefix, array(
		'id'     => 'genearl_settings',
		'title'  => esc_html__( 'General Options', 'private-store' ),
		'icon'   => 'fas fa-sliders-h',
		'fields' => $section_1_fields
	 ));

	// Visibility Options
	$visibility_fields = array();
	$visibility_fields[] = array(
		'id'      => $prefix. 'notification_on_mobile',
		'type'    => 'select',
		'title'   => esc_html__( 'Hide on mobile device', 'hashbar' ),
		'options' => array(
			'on'  => esc_html__( 'No', 'hashbar' ),
			'off' => esc_html__( 'Yes', 'hashbar' ),
		),
		'default'	=> 'on',
	);
	$visibility_fields[] = array(
		'id'      => $prefix. 'notification_on_desktop',
		'type'    => 'select',
		'title'   => esc_html__( 'Hide on desktop device', 'hashbar' ),
		'options' => array(
			'on'  => esc_html__( 'No', 'hashbar' ),
			'off' => esc_html__( 'Yes', 'hashbar' ),
		),
		'default'	=> 'on',
	);
	$visibility_fields[] = array(
		'id'      => $prefix. 'show_hide_scroll',
		'type'    => 'select',
		'title'   => esc_html__( 'Show/Hide notification based on scroll position', 'hashbar' ),
		'options' => array(
		    'show_hide_scroll_enable' => esc_html__( 'Enable', 'hashbar' ),
		    'show_hide_scroll_disable'=> esc_html__( 'Disable', 'hashbar' ),
		),
		'default' => 'show_hide_scroll_disable',
	);

	$visibility_fields[] = array(
		'id'         => $prefix. 'show_scroll_position',
		'type'       => 'text',
		'title'      => esc_html__( 'Scroll position to show', 'hashbar' ),
		'desc'       => esc_html__( 'Enter the scroll position value. You can use either use a fixed number or a percentage value. Example: 50 or 50%', 'hashbar' ),
		'default'    => '15%',
		'dependency' => array( $prefix. 'show_hide_scroll', '==', 'show_hide_scroll_enable' ),
	);

	$visibility_fields[] = array(
		'id'         => $prefix. 'hide_scroll_position',
		'type'       => 'text',
		'title'      => esc_html__( 'Scroll position to hide', 'hashbar' ),
		'desc'       => esc_html__( 'Enter the scroll position value. You can use either use a fixed number or a percentage value. Example: 50 or 50%', 'hashbar' ),
		'default'    => '75%',
		'dependency' => array( $prefix. 'show_hide_scroll', '==', 'show_hide_scroll_enable' ),
	);

	$visibility_fields[] = array(
	    'id'    => $prefix. 'notification_how_many_times_to_show',
	    'type'  => 'text',
	    'title' => esc_html__( 'How many times to show this notification', 'hashbar' ),
	    'desc'  => esc_html__( 'Input the number, how many times will appear this notification. Number consider by each page load where the notification appears', 'hashbar' ),
	);

	$visibility_fields[] = array(
		'id'      => $prefix. 'notification_schedule',
		'type'    => 'select',
		'title'   => esc_html__( 'Schedule notification expiry date/time', 'hashbar' ),
		'desc'    => __( 'Enable this, when you want to get disabled this notification at a specific date/time. <br>After reaching the assigned date/time this notification will be disabled automatically.', 'hashbar'),
		'options' => array(
			'on'  => esc_html__( 'Enable', 'hashbar' ),
			'off' => esc_html__( 'Disable', 'hashbar' ),
		),
		'default'	=> 'off',
		'class'		=> 'hashbar_pro_notice',
	);
	$visibility_fields[] = array(
		'id'       => $prefix. 'notification_schedule_datetime',
		'type'     => 'callback',
		'function' => 'hashbar_custom_date_field_callback',
		'title'    => esc_html__('Expiry date/time', 'hashbar'),
		'args'     => array(
			'desc' 	   => __( 'Set a Date/Time after which the notification will get disabled. </br> The scheduled date/time must be greater than current date/time, otherwise this notification will be saved as draft. </br>Your current time is: '. current_time(get_option( 'date_format')) .' '. current_time('h : i A') . ' If you see it is not correct, set the correct timezone of your location from Settings > General > Timezone or  <a target="_blank" href="'.admin_url( 'options-general.php').'">click here</a>', 'hashbar'),
			'field_name' => $prefix. 'notification_schedule_datetime',

		),
		'class'		=> 'hashbar_pro_opacity',
	);
	CSF::createSection( $prefix, array(
		'id'     => 'genearl_settings',
		'title'  => esc_html__( 'Visibility Options', 'private-store' ),
		'icon'   => 'far fa-eye',
		'fields' => $visibility_fields
	 ));

	// Design
	$design_fields = array();

	$design_fields[] = array(
		'id'    => $prefix. 'notification_content_heading',
		'type'  => 'subheading',
		'style' => 'success',
		'title' => esc_html__( 'Content Area', 'hashbar' ),
	);
	$design_fields[] = array(
	    'id'    => $prefix. 'notification_content_bg_color',
	    'type'  => 'color',
	    'title' => esc_html__( 'Content background color', 'hashbar' ),
	);

	$design_fields[] = array(
	    'id'    => $prefix. 'notification_content_bg_image',
	    'type'  => 'media',
	    'title' => esc_html__( 'Content background image', 'hashbar' ),
	);

	$design_fields[] = array(
	    'id'    => $prefix. 'notification_content_text_color',
	    'type'  => 'color',
	    'title' => esc_html__( 'Content text color', 'hashbar' ),
	);

	$design_fields[] = array(
	    'id'    => $prefix. 'notification_content_bg_opcacity',
	    'type'  => 'text',
	    'title' => esc_html__( 'Content opacity', 'hashbar' ),
	    'desc' 	=> esc_html__( 'Example: 0.5', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'       => $prefix. 'notification_content_margin',
		'type'     => 'callback',
		'function' => 'hashbar_spacing_field_callback',
		'title'    => esc_html__( 'Content margin', 'hashbar' ),
		'class'    => 'csf-field-spacing',
	    'args' => array(
	        'for' => 'margin',
	        'id'  => $prefix. 'notification_content_margin'
	    )
	);

	$design_fields[] = array(
		'id'       => $prefix. 'notification_content_padding',
		'type'     => 'callback',
		'function' => 'hashbar_spacing_field_callback',
		'title'    => esc_html__( 'Content padding', 'hashbar' ),
		'class'    => 'csf-field-spacing',
	    'args' 	   => array(
	        'for' => 'padding',
	        'id'  => $prefix. 'notification_content_padding'
	    )
	);
	
	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_heading',
		'type'  => 'subheading',
		'title' => esc_html__( 'Close Button', 'hashbar' ),
	);
	
	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_bg_color',
		'type'  => 'color',
		'title' => esc_html__( 'BG color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_color',
		'type'  => 'color',
		'title' => esc_html__( 'Color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_hover_color',
		'type'  => 'color',
		'title' => esc_html__( 'Hover color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_hover_bg_color',
		'type'  => 'color',
		'title' => esc_html__( 'Hover BG color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_close_button_heading',
		'type'  => 'subheading',
		'title' => esc_html__( 'Open button', 'hashbar' ),
	);
	

	$design_fields[] = array(
		'id'    => $prefix. 'notification_arrow_bg_color',
		'type'  => 'color',
		'title' => esc_html__( 'BG color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_arrow_color',
		'type'  => 'color',
		'title' => esc_html__( 'Color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_arrow_hover_color',
		'type'  => 'color',
		'title' => esc_html__( 'Hover color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_arrow_hover_bg_color',
		'type'  => 'color',
		'title' => esc_html__( 'Hover BG color', 'hashbar' ),
	);

	$design_fields[] = array(
		'id'    => $prefix. 'notification_button_heading',
		'type'  => 'subheading',
		'title' => esc_html__( 'Call to Action Button', 'hashbar' ),
	);
	$design_fields[] = array(
		'id'       => $prefix. 'notification_button_margin',
		'type'     => 'callback',
		'function' => 'hashbar_spacing_field_callback',
		'title'    => esc_html__( 'Margin', 'hashbar' ),
		'class'    => 'csf-field-spacing',
	    'args' => array(
	        'for' => 'button_margin',
	        'id'  => $prefix. 'notification_button_margin'
	    )
	);

	$design_fields[] = array(
		'id'       => $prefix. 'notification_button_padding',
		'type'     => 'callback',
		'function' => 'hashbar_spacing_field_callback',
		'title'    => esc_html__( 'Padding', 'hashbar' ),
		'class'    => 'csf-field-spacing',
	    'args' => array(
	        'for' => 'button_padding',
	        'id'  => $prefix. 'notification_button_padding'
	    )
	);

	if(!hashbar_wpnb_is_classic_editor_plugin_active()){
		$design_fields[] = array(
			'id'    => $prefix. 'promo_banner_heading',
			'type'  => 'subheading',
			'title' => esc_html__( 'Promo Banner', 'hashbar' ),
		);
		$design_fields[] = array(
			'id'       => $prefix. 'prb_margin',
			'type'     => 'callback',
			'function' => 'hashbar_spacing_field_callback',
			'title'    => esc_html__( 'Spacing', 'hashbar' ),
			'class'    => 'csf-field-spacing',
			'args'     => array(
				'for'  => 'margin',
				'id'   => $prefix. 'prb_margin',
				'desc' => wp_kses(__('Enter margin value for top, right, bottom, left of Promo Banner. Use unit px / %. Example: 70px 5% 5px 5px.<br>Default Values: Top:50px, Right:50px, Bottom:50px, Left:50px','hashbar'),array( 'br' => array() ) ),
		    )
		);
	}

	CSF::createSection( $prefix, array(
		'id'     => 'design',
		'title'  => esc_html__( 'Design', 'hashbar' ),
		'icon'   => 'fas fa-eye-dropper',
		'fields' => $design_fields
	) );

	//countdown options
	$countdown_fields = array();

	$countdown_fields[] = array(
		'id'      => $prefix. 'count_down',
		'type'    => 'select',
		'title'   => esc_html__( 'Enable countdown', 'hashbar' ),
		'options' => array(
		    'ntf_countdown_enable' => esc_html__( 'Yes', 'hashbar' ),
		    'ntf_countdown_disable'=> esc_html__( 'No', 'hashbar' ),
		),
		'default' => 'ntf_countdown_disable',
	);

	$countdown_fields[] = array(
		'id'       => $prefix. 'countdown_schedule_datetime',
		'type'     => 'callback',
		'function' => 'hashbar_custom_date_field_callback',
		'title'    => esc_html__('Countdown date', 'hashbar'),
		'args'     => array(
			'desc' => __( 'Set a Date after which the countdown will get disabled. </br> The countdown date must be greater than current date/time.', 'hashbar'),
			'field_name' => $prefix. 'countdown_schedule_datetime'
		),
		'dependency'		 => array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	$countdown_fields[] = array(
      'id'      	=> $prefix. 'countdown_style',
      'type'    	=> 'select',
      'title'   	=> 'Countdown style',
      'placeholder' => 'Select an option',
      'options' => array(
        'style-1' => 'Style One',
        'style-2' => 'Style Two',
        'style-3' => 'Style Three',
        'style-4' => 'Style Four',
        'style-5' => 'Style Five',
        'style-6' => 'Style Six',
        'style-7' => 'Style Seven',
      ),
      'default' => 'style-7',
      'class'   => 'hthb-countdown-style-demo',
      'dependency' => array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
    );

    $countdown_fields[] = array(
      'id'      => $prefix. 'countdown_position',
      'type'    => 'radio',
      'title'   => 'Countdown placement',
      'inline'  => true,
      'options' => array(
        'row' => 'Left',
        'center' => 'Center',
        'row-reverse' => 'Right',
        'shortcode' => 'Use Shortcode',
      ),
      'default' => 'row',
      'class' 	=> 'hashbar_pro_notice_radiio',
      'dependency' => array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
    );

    $countdown_fields[] = array(
        'type'       => 'notice',
        'style'      => 'info',
        'content'    => __( 'Pase this shortcode <code>[hashbar_countdown]</code> anywhere in the content editor above, to show the countdown.', 'hashbar' ),
        'dependency' => array( $prefix. 'countdown_position|'.$prefix. 'count_down', '==|==', 'shortcode|ntf_countdown_enable' )
    );

	$countdown_fields[] = array(
	  'id'      => $prefix. 'countdown_cudtomize_label',
	  'type'    => 'checkbox',
	  'title'   => __( 'Custom labels', 'hashbar'),
	  'label'   => 'Yes',
	  'default' => false,
	  'dependency'		 => array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	$countdown_fields[] = array(
		'id'         => $prefix. 'notification_countdown_day_txt',
		'type'       => 'text',
		'title'      => esc_html__( 'Days', 'hashbar' ),
		'default' 	 => 'Days',
		'dependency' => array($prefix. 'countdown_cudtomize_label|'.$prefix. 'count_down', '==|==', '1|ntf_countdown_enable'),
	);

	$countdown_fields[] = array(
		'id'         => $prefix. 'notification_countdown_hour_txt',
		'type'       => 'text',
		'title'      => esc_html__( 'Hours', 'hashbar' ),
		'default' 	 => 'Hours',
		'dependency' => array($prefix. 'countdown_cudtomize_label|'.$prefix. 'count_down', '==|==', '1|ntf_countdown_enable'),
	);

	$countdown_fields[] = array(
		'id'         => $prefix. 'notification_countdown_mins_txt',
		'type'       => 'text',
		'title'      => esc_html__( 'Minutes', 'hashbar' ),
		'default' 	 => 'Minutes',
		'dependency' => array($prefix. 'countdown_cudtomize_label|'.$prefix. 'count_down', '==|==', '1|ntf_countdown_enable'),
	);

	$countdown_fields[] = array(
		'id'         => $prefix. 'notification_countdown_sec_txt',
		'type'       => 'text',
		'title'      => esc_html__( 'Seconds', 'hashbar' ),
		'default' 	 => 'Seconds',
		'dependency' => array($prefix. 'countdown_cudtomize_label|'.$prefix. 'count_down', '==|==', '1|ntf_countdown_enable'),
	);

	$countdown_fields[] = array(
		'id'      => $prefix. 'countdown_time',
		'type'    => 'checkbox',
		'title'   => 'Hide timers',
		'inline'  => true,
		'options' => array(
		    'hide_day' 	 => 'Hide Days',
		    'hide_hours' => 'Hide Hours',
		    'hide_mins'  => 'Hide Minutes',
		    'hide_sec'   => 'Hide Seconds',
		),
		'class' 	 => 'hashbar_pro_notice',
		'dependency' => array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	$countdown_fields[] = array(
	  'id'      => $prefix. 'countdown_time_label',
	  'type'    => 'checkbox',
	  'title'   => 'Hide labels',
	  'label'   => 'Yes',
	  'default' => false,
	  'class' 	 => 'hashbar_pro_notice',
	  'dependency'=> array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	$countdown_fields[] = array(
		'id'    => $prefix. 'countdown_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Style', 'hashbar' ),
		'dependency'=> array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	$countdown_fields[] = array(
	      'id'         => $prefix. 'countdown_timer_style',
	      'type'       => 'accordion',
	      'accordions' => array(
	        array(
	          'title'  => 'Timer Item',
	          'fields' => array(
	            array(
					'id'    => 'countdown_bg_color',
					'type'  => 'color',
					'title' => esc_html__( 'Background', 'hashbar' ),
				),
				array(
			      'id'    => 'countdown_box_border',
			      'type'  => 'border',
			      'title' => 'Border',
			      'all'   => true,
			      'default'  => array(
			      	'all'  => '1',
			      )
			    ),
			    array(
					'id'    => 'countdown_brdr_rdus',
					'type'  => 'number',
					'title' =>  esc_html__( 'Border radius', 'hashbar' ),
					'unit'  => 'px'
				),
				array(
					'id'    => 'countdown_box_height',
					'type'  => 'number',
					'title' =>  esc_html__( 'Height', 'hashbar' ),
					'unit'  => 'px',
				),
				array(
					'id'    => 'countdown_box_width',
					'type'  => 'number',
					'title' =>  esc_html__( 'Width', 'hashbar' ),
					'unit'  => 'px',
				),
				array(
					'id'    => 'countdown_box_spacing',
					'type'  => 'number',
					'title' =>  esc_html__( 'Spacing', 'hashbar' ),
					'unit'  => 'px',
				),
				array(
			      'id'          => 'countdown_box_padding',
			      'type'        => 'dimensions',
			      'title'       => 'Padding',
			      'width_icon'  => 'top-bottom',
			      'height_icon' => 'left-right',
			      'units'       => array( 'px' ),
			      'default'     => array(
			        'width'     => '',
			        'height'    => '',
			      ),
			      'class'		=> 'hthb-disable-placeholder-for-padding',
			    )
	          )
	        ),
	        array(
	          'title'  => 'Timer Number',
	          'fields' => array(
	            array(
					'id'    => 'countdown_timer_bg_color',
					'type'  => 'color',
					'title' => esc_html__( 'Background', 'hashbar' )
				),
				array(
					'id'    => 'countdown_timer_txt_color',
					'type'  => 'color',
					'title' => esc_html__( 'Text color', 'hashbar' )
				),
				array(
			      'id'             => 'timr_number_typhography',
			      'type'           => 'typography',
			      'title'          => 'Typography',
			      'text_align'     => false,
			      'text_transform' => false,
			      'color'          => false,
			      'font_family'    => false,
			      'default'        => array(
			        'font-family'  => 'Lato',
			        'font-weight'  => '400',
			        'subset'       => 'latin',
			        'type'         => 'google',
			      )
			    ),
			    array(
			      'id'    => 'countdown_tmr_box_border',
			      'type'  => 'border',
			      'title' => 'Border',
			      'all'   => true,
			      'default'  => array(
			      	'all'  => '1',
			      ),
			    ),
			    array(
					'id'    => 'countdown_timer_border_radius',
					'type'  => 'number',
					'title' => esc_html__( 'Border radius', 'hashbar' ),
					'unit'    => 'px'
				),
				array(
					'id'    => 'countdown_timer_min_width',
					'type'  => 'number',
					'title' => esc_html__( 'Min Width', 'hashbar' ),
					'unit'  => 'px',
				),
				array(
			      'id'          => 'countdown_timer_box_padding',
			      'type'        => 'dimensions',
			      'title'       => 'Padding',
			      'width_icon'  => 'top-bottom',
			      'height_icon' => 'left-right',
			      'units'       => array( 'px' ),
			      'default'     => array(
			        'width'     => '',
			        'height'    => '',
			      ),
			      'class'		=> 'hthb-disable-placeholder-for-padding',
			    )
	          )
	        ),
	        array(
	          'title'  => 'Label',
	          'fields' => array(
	            array(
					'id'    => 'countdown_label_bg_color',
					'type'  => 'color',
					'title' => esc_html__( 'Background', 'hashbar' )
				),
				array(
					'id'    => 'countdown_label_color',
					'type'  => 'color',
					'title' => esc_html__( 'Text color', 'hashbar' )
				),
				array(
			      'id'             => 'countdown_timr_label_typhography',
			      'type'           => 'typography',
			      'title'          => 'Typography',
			      'text_align'     => false,
			      'text_transform' => false,
			      'color'          => false,
			      'font_family'    => false,
			      'default'        => array(
			        'font-family'  => 'Lato',
			        'font-weight'  => '400',
			        'subset'       => 'latin',
			        'type'         => 'google',
			      )
			    ),
			    array(
			      'id'    => 'countdown_tmr_label_border',
			      'type'  => 'border',
			      'title' => 'Border',
			      'all'   => true,
			      'default'  => array(
			      	'all'  => '1',
			      ),
			    ),
			    array(
					'id'    => 'countdown_label_border_radius',
					'type'  => 'number',
					'title' => esc_html__( 'Border radius', 'hashbar' ),
					'unit'    => 'px'
				),
				array(
			      'id'          => 'countdown_timer_label_padding',
			      'type'        => 'dimensions',
			      'title'       => 'Padding',
			      'width_icon'  => 'top-bottom',
			      'height_icon' => 'left-right',
			      'units'       => array( 'px' ),
			      'default'     => array(
			        'width'     => '',
			        'height'    => '',
			      ),
			      'class'		=> 'hthb-disable-placeholder-for-padding',
			    )
	          )
	        )
	    ),
	    'dependency'=> array( $prefix. 'count_down', '==', 'ntf_countdown_enable' ),
	);

	CSF::createSection( $prefix, array(
		'id'     => 'countdwon_settings',
		'title'  => esc_html__( 'Countdown', 'private-store' ),
		'icon'   => 'fas fa-stopwatch',
		'fields' => $countdown_fields
	));
 }