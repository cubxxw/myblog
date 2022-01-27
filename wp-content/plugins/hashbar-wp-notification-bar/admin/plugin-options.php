<?php
add_action( 'admin_menu', 'hashbar_wpnb_add_admin_menu' );
add_action( 'admin_init', 'hashbar_wpnb_settings_init' );


function hashbar_wpnb_add_admin_menu(  ) { 

	add_submenu_page( 'edit.php?post_type=wphash_ntf_bar', 'Settings', 'Settings', 'manage_options', 'hashbar_options_page', 'hashbar_wpnb_options_page' );
	if(hashbar_wpnb_get_opt('enable_analytics')){
		add_submenu_page( 'edit.php?post_type=wphash_ntf_bar', 'HashBar Analytics', 'Analytics', 'manage_options', 'hashbar_analytics_page', 'hashbar_wpnb_analytics_page' );
	}

}


function hashbar_wpnb_settings_init(  ) { 

	register_setting( 'options_group_1', 'hashbar_wpnb_opt' );

	add_settings_section(
		'hashbar_wpnb_options_group_1_section', 
		'', 
		null, 
		'options_group_1'
	);

	add_settings_field( 
		'dont_show_bar_after_close', 
		__( 'Don\'t Show Notification After Close', 'hashbar' ), 
		'hashbar_wpnb_checkbox_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	add_settings_field( 
		'keep_closed_bar', 
		__( 'Keep The Notification Bar Closed', 'hashbar' ), 
		'hashbar_wpnb_bar_closed_checkbox_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	add_settings_field( 
		'enable_analytics', 
		__( 'Enable Analytics', 'hashbar' ), 
		'hashbar_wpnb_analytics_checkbox_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	add_settings_field( 
		'count_onece_byip', 
		__( 'Count only 1 from each IP', 'hashbar' ), 
		'hashbar_wpnb_count_onece_byip_checkbox_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	add_settings_field( 
		'analytics_from', 
		__( 'Analytics From', 'hashbar' ), 
		'hashbar_wpnb_analytics_from_options_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	add_settings_field( 
		'mobile_device_breakpoint', 
		__( 'Mobile device breakpoint (px)', 'hashbar' ), 
		'hashbar_wpnb_text_render', 
		'options_group_1', 
		'hashbar_wpnb_options_group_1_section' 
	);

	// dismiss the admin notice for user
	$user_id = get_current_user_id();
    if ( isset( $_GET['hthb-notice-dismissed'] ) ){
        add_user_meta( $user_id, 'hthb_notice_dismissed', 'true', true );
    }

}


function hashbar_wpnb_checkbox_render(  ) { 

	$options = get_option( 'hashbar_wpnb_opt' );
	$checkbox_val = isset($options['dont_show_bar_after_close']) ? $options['dont_show_bar_after_close'] : '';
	?>
	<input class="pro" type='checkbox' name='hashbar_wpnb_opt[dont_show_bar_after_close]'  <?php checked($checkbox_val, 1) ?> value='1'>
	<p class="description"><?php echo esc_html__('If check this option. The notification will not appear again on a page, after closing the notification.', 'hashbar');?></p>
	<?php

}

function hashbar_wpnb_bar_closed_checkbox_render(){
	$options = get_option( 'hashbar_wpnb_opt' );
	$checkbox_val = isset($options['keep_closed_bar']) ? $options['keep_closed_bar'] : '';
	?>
	<input type='checkbox' name='hashbar_wpnb_opt[keep_closed_bar]'  <?php checked($checkbox_val, 1) ?> value='1'>
	<p class="description"><?php echo __( 'When you close the notification bar once then it will always keep closed in all pages of your site.<br>This option will be effective for the notifications which have set "Load as minimized = No" from the notification metabox options.','hashbar' ); ?></p>
	<?php
}

function hashbar_wpnb_analytics_checkbox_render(){
	$options = get_option( 'hashbar_wpnb_opt' );
	$checkbox_val = isset($options['enable_analytics']) ? $options['enable_analytics'] : '';
	?>
	<input type='checkbox' name='hashbar_wpnb_opt[enable_analytics]'  <?php checked($checkbox_val, 1) ?> value='1'>
	<p class="description"><?php echo esc_html__('Enable Analytics to get the analytical report about your notifications.', 'hashbar');?></p>
	<?php
}

function hashbar_wpnb_count_onece_byip_checkbox_render(){
	$options = get_option( 'hashbar_wpnb_opt' );
	$checkbox_val = isset($options['count_onece_byip']) ? $options['count_onece_byip'] : '';
	?>
	<input type='checkbox' name='hashbar_wpnb_opt[count_onece_byip]'  <?php checked($checkbox_val, 1) ?> value='1'>
	<p class="description"><?php echo esc_html__('Enable to count the views and clicks only once from each IP-address.', 'hashbar');?></p>
	<?php
}

function hashbar_wpnb_analytics_from_options_render(){
	$options = get_option( 'hashbar_wpnb_opt' );
	$checkbox_val = isset($options['analytics_from']) ? $options['analytics_from'] : '';
	?>
	<select name="hashbar_wpnb_opt[analytics_from]">
	  <option value="everyone" <?php echo $checkbox_val == 'everyone' ? 'selected' : ''; ?>>Everyone</option>
	  <option value="guests" <?php echo $checkbox_val == 'guests' ? 'selected' : ''; ?>>Guest Only</option>
	  <option value="registered_users" <?php echo $checkbox_val == 'registered_users' ? 'selected' : ''; ?>>Rigestered Users Only</option>
	</select>
	<?php
}

function hashbar_wpnb_text_render(  ) {

	$options = get_option( 'hashbar_wpnb_opt' );
	$text_val = isset($options['mobile_device_breakpoint']) ? $options['mobile_device_breakpoint'] : '';
	?>
	<input type='text' name='hashbar_wpnb_opt[mobile_device_breakpoint]' value="<?php echo esc_attr($text_val); ?>">
	<p class="description">Sets the breakpoint between mobile and desktop devices. Below this breakpoint mobile layout will appear (Default: 767).</p>
	<?php

}



function hashbar_wpnb_options_page(  ) { 

	?>
	<form id="hashbar" action='options.php' method='post'>

		<h2><?php echo esc_html__( 'HashBar Pro Global Options', 'hashbar' ) ?></h2>

		<?php
		settings_fields( 'options_group_1' );
		do_settings_sections( 'options_group_1' );
		submit_button();
		?>
	</form>
	<?php

}

function hashbar_wpnb_analytics_page(){
	$total_traking 		= false != get_transient( 'total_hthb_analytics_count' ) ? get_transient( 'total_hthb_analytics_count' ) : array(); 
	$postwise_traking   = false != get_transient( 'postwise_hthb_analytics_count' ) ? get_transient( 'postwise_hthb_analytics_count' ) : array();
	$country_traking    = false != get_transient( 'countrywise_hthb_analytics_count' ) ? get_transient( 'countrywise_hthb_analytics_count' ) : array();

	$trk_lenght   = count($total_traking);
	$total_clicks = $trk_lenght > 0 && !is_null($total_traking[0]['totalclicks']) ? $total_traking[0]['totalclicks'] : 0;
	$total_views  = $trk_lenght > 0 && !is_null($total_traking[0]['totalviews']) ? $total_traking[0]['totalviews'] : 0;
	$total_clthrt = $trk_lenght > 0 && !is_null($total_traking[0]['totalviews']) && !is_null($total_traking[0]['totalclicks'])? round(($total_traking[0]['totalclicks']/$total_traking[0]['totalviews'])*100, 2) : 0;

	?>
	<div class="hthb--site-wrapper-reveal">
	    <div class="hthb--container">
	    	<div class="hthb-analytics-title">
    			<h2><?php echo esc_html( 'Analytics Overview','hashbar'); ?></h2>
    		</div>
	        <div class="hthb--row">
	            <div class="hthb--col-lg-4 hthb--col-md-4 hthb--col-sm-6 ">
	                <div class="hthb-card__box">
	                    <div class="hthb-card__icon">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/click-icons.png" alt="">
	                    </div>
	                    <div class="hthb-card__content">
	                        <h6 class="hthb-card__title"><?php echo esc_html( 'Total Clicks','hashbar') ?></h6>
	                        <h4 class="hthb-card__nubmer"><?php echo $total_clicks; ?></h4>
	                    </div>
	                    <div class="hthb-card__inner-image">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/views-icons-bg.png" alt="">
	                    </div>
	                </div>
	            </div>
	            <div class="hthb--col-lg-4 hthb--col-md-4 hthb--col-sm-6 ">
	                <div class="hthb-card__box hthb-card__box--two">
	                    <div class="hthb-card__icon hthb-card__icon--two">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/views-icons.png" alt="">
	                    </div>
	                    <div class="hthb-card__content">
	                        <h6 class="hthb-card__title"><?php echo esc_html( 'Total Views','hashbar') ?></h6>
	                        <h4 class="hthb-card__nubmer hthb-card__nubmer--two"><?php echo $total_views; ?></h4>
	                    </div>
	                    <div class="hthb-card__inner-image">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/click-icons-bg.png" alt="">
	                    </div>
	                </div>
	            </div>
	            <div class="hthb--col-lg-4 hthb--col-md-4 hthb--col-sm-6 ">
	                <div class="hthb-card__box hthb-card__box--three">
	                    <div class="hthb-card__icon hthb-card__icon--three">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/rate-icons.png" alt="">
	                    </div>
	                    <div class="hthb-card__content">
	                        <h6 class="hthb-card__title"><?php echo esc_html( 'Click Through Rate','hashbar') ?></h6>
	                        <h4 class="hthb-card__nubmer hthb-card__nubmer--three"><?php echo $total_clthrt; ?>%</h4>
	                    </div>
	                    <div class="hthb-card__inner-image">
	                        <img src="<?php echo HASHBAR_WPNB_URI; ?>/admin/img/rate-icons-bg.png" alt="">
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="hthb-traking-by-notification-area">
	    <div class="hthb--container">
	        <div class="hthb--row">
	            <div class="hthb--col-lg-8">
	                <div class="hthb-traking">
	                    <h6 class="hthb-traking__heading"><?php echo esc_html( 'Track the analytics for each notification bar','hashbar') ?></h6>

	                    <div class="hthb-traking__wrap">
	                        <div class="hthb-traking__header">
	                            <div class="hthb-traking__header-item hthb-traking__header-item--name">
	                                <?php echo esc_html( 'Name','hashbar'); ?>
	                            </div>
	                            <div class="hthb-traking__header-item hthb-traking__header-item--views">
	                                <?php echo esc_html( 'Total Views','hashbar'); ?>
	                            </div>
	                            <div class="hthb-traking__header-item hthb-traking__header-item--clicks">
	                                <?php echo esc_html( 'Total Clicks','hashbar'); ?>
	                            </div>
	                            <div class="hthb-traking__header-item hthb-traking__header-item--through-rate">
	                                <?php echo esc_html( 'CTR','hashbar'); ?>
	                            </div>
	                        </div>
	                        <div class="hthb-traking__body">
	                        	<?php 
	                        	$args = array( 'post_type' => 'wphash_ntf_bar', 'posts_per_page' => -1 );
        						$ntf_analytics_query = new WP_Query($args);
	                        	while( $ntf_analytics_query->have_posts() ):

	                        		$ntf_analytics_query->the_post();
	                        		$post_id = get_the_id(); ?>

	                        		<?php if('publish' == get_post_status($post_id)):?>
			                            <div class="hthb-traking__items">
			                                <div class="hthb-traking__item hthb-traking__item--title">
			                                    <?php echo get_the_title($post_id); ?>
			                                </div>
			                                <div class="hthb-traking__item hthb-traking__item--total-views-number">0</div>
			                                <div class="hthb-traking__item hthb-traking__item--total-clicks-number">0</div>
			                                <div class="hthb-traking__item hthb-traking__item--through-rate-numbmer">0 %</div>
			                            </div>
		                        	<?php endif; ?>

		                        <?php endwhile; ?>
	                        </div>
	                    </div>
	                </div>
	                <a target="_blank" class="hthb-pro-link" href="<?php echo esc_url('https://hasthemes.com/plugins/wordpress-notification-bar-plugin/') ?>"><?php echo esc_html__('Upgrade to Pro','hashbar') ?></a>
	            </div>
	            <div class="hthb--col-lg-4 ">
	                <div class="hthb-top-countries">
	                    <h6 class="hthb-top-countries__heading"><?php echo esc_html( 'Top 10 Countries','hashbar') ?></h6>
	                    <?php foreach ($country_traking as $countrywise_count):?>
		                    <div class="hthb-top-countries__list-wrap">
		                        <div class="hthb-top-countries__item">
		                            <div class="hthb-top-countries__name"><?php echo $countrywise_count['country']; ?></div>
		                        </div>
		                    </div>
		                <?php endforeach; ?>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
	<?php
}

?>