<?php get_header(); ?>

<div class="content">

	<?php if ( get_theme_mod('heading-enable','off') == 'on' ) : ?>
		<?php get_template_part('inc/page-title'); ?>
	<?php endif; ?>
	
	<?php get_template_part('inc/featured'); ?>
	<?php get_template_part('inc/highlights'); ?>
	
	<?php get_template_part('inc/front-widgets-top'); ?>
	
	<?php if ( have_posts() ) : ?>

		<div class="article-grid-wrap">
			<div class="article-grid">
				<?php while ( have_posts() ): the_post(); ?>
					<?php get_template_part('content'); ?>
				<?php endwhile; ?>
			</div>
		</div>

		<?php get_template_part('inc/front-widgets-bottom'); ?>
		<?php get_template_part('inc/pagination'); ?>
		
	<?php endif; ?>
	
</div><!--/.content-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>