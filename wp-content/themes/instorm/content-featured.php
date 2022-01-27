<?php $format = get_post_format(); ?>

<div class="featured-item-wrap">
	<div class="featured-item" style="background-image:url('<?php the_post_thumbnail_url('instorm-large'); ?>');">
		<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-play"></i></span>'; ?>
		<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-volume-up"></i></span>'; ?>
		<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-star"></i></span>'; ?>
		<a class="featured-link" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		<div class="featured-content">
			<h3 class="featured-title"><?php the_title(); ?></h3>
			<div class="featured-date">
				<i class="far fa-calendar"></i> <?php the_time( get_option('date_format') ); ?>
			</div>
		</div>
		<?php if ( comments_open() && ( get_theme_mod( 'comment-count', 'on' ) == 'on' ) ): ?>
			<a class="post-comments" href="<?php comments_link(); ?>"><span><?php comments_number( '0', '1', '%' ); ?></span></a>
		<?php endif; ?>	
	</div>
</div>