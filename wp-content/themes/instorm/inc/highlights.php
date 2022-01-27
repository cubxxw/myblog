<?php
// Query highlight entries
$highlight = new WP_Query(
	array(
		'no_found_rows'				=> false,
		'update_post_meta_cache'	=> false,
		'update_post_term_cache'	=> false,
		'ignore_sticky_posts'		=> 1,
		'posts_per_page'			=> absint( get_theme_mod('highlight-posts-count','6') ),
		'cat'						=> absint( get_theme_mod('highlight-category','') )
	)
);
?>

<?php if ( is_home() && !is_paged() && ( get_theme_mod('highlight-posts-count','6') !='0') || is_single() || is_archive() || is_search() || is_404() || is_page()  ): ?>

<div class="slick-highlights-wrap-outer">	
	<div class="slick-highlights-wrap container-inner">
		<div class="slick-highlights">
			<?php while ( $highlight->have_posts() ): $highlight->the_post(); ?>
				<div>	
					<?php get_template_part('content-highlight'); ?>
				</div>
			<?php endwhile; ?>
		</div>
		<div class="slick-highlights-nav"></div>
	</div>
</div>

<?php wp_reset_postdata(); ?>

<?php endif; ?>