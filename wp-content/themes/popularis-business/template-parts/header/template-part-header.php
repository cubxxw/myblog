<div class="header-part">
    <div class="container">
        <div class="site-heading navbar-brand <?php echo esc_attr(is_active_sidebar('popularis-business-header') ? 'col-md-3' : 'col-md-12') ?>" >
            <div class="site-branding-logo">
                <?php the_custom_logo(); ?>
            </div>
            <div class="site-branding-text">
                <?php if (is_front_page()) : ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                <?php else : ?>
                    <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
                <?php endif; ?>

                <?php
                $description = get_bloginfo('description', 'display');
                if ($description || is_customize_preview()) :
                    ?>
                    <p class="site-description">
                        <?php echo esc_html($description); ?>
                    </p>
                <?php endif; ?>
            </div><!-- .site-branding-text -->
        </div>

        <?php if (is_active_sidebar('popularis-business-header')) { ?>
            <?php dynamic_sidebar('popularis-business-header'); ?>	
        <?php } ?>
    </div>
</div>    
<?php do_action('popularis_before_menu'); ?> 
<div class="main-menu">
    <nav id="site-navigation" class="navbar navbar-default">     
        <div class="container">   
            <div class="navbar-header">
                <?php if (function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('main_menu')) : ?>
                <?php elseif (has_nav_menu('main_menu')) : ?>

                    <?php if (function_exists('popularis_header_cart') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-cart visible-xs" >
                            <?php popularis_header_cart(); ?>
                        </div>	
                    <?php } ?>
                    <?php if (function_exists('popularis_my_account') && class_exists('WooCommerce')) { ?>
                        <div class="mobile-account visible-xs" >
                            <?php popularis_my_account(); ?>
                        </div>
                    <?php } ?>
                    <a href="#my-menu" id="main-menu-panel" class="open-panel visible-xs" data-panel="main-menu-panel">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php
            if (is_front_page() && has_nav_menu('main_menu_home')) {
                $the_menu = 'main_menu_home';
            } else {
                $the_menu = 'main_menu';
            }
            wp_nav_menu(array(
                'theme_location' => $the_menu,
                'depth' => 5,
                'container_id' => 'my-menu',
                'container' => 'nav',
                'container_class' => 'menu-container',
                'menu_class' => 'nav navbar-nav navbar-left',
                'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
                'walker' => new wp_bootstrap_navwalker(),
            ));
            ?>
        </div>
        <?php do_action('popularis_menu'); ?>
    </nav> 
</div>
<?php do_action('popularis_after_menu'); ?>
