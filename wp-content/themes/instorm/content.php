<?php $format = get_post_format(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('entry-list group'); ?>>	
	
		<?php if ( has_post_thumbnail() ): ?>
		<div class="entry-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('instorm-medium'); ?>
				<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-play"></i></span>'; ?>
				<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-volume-up"></i></span>'; ?>
				<?php if ( is_sticky() ) echo'<span class="thumb-icon small"><i class="fas fa-star"></i></span>'; ?>
			</a>
		</div>
		<?php endif; ?>
		
	<div class="entry-list-inner <?php if ( has_post_thumbnail() ) echo 'entry-thumbnail-enabled'; ?>">	
		
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2><!--/.entry-title-->
		
		<?php if ( comments_open() && ( get_theme_mod( 'comment-count', 'on' ) == 'on' ) ): ?>
			<a class="post-comments" href="<?php comments_link(); ?>"><span><?php comments_number( '0', '1', '%' ); ?></span></a>
		<?php endif; ?>	
		
		<ul class="entry-meta group">
			<li class="entry-category"><?php the_category(' / '); ?></li>
			<li class="entry-date"><i class="far fa-calendar"></i><?php the_time( get_option('date_format') ); ?></li>
		</ul>
		
		<?php if (get_theme_mod('excerpt-length','26') != '0'): ?>
			<div class="clear"></div>
			<div class="entry-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>
		
	</div>
	
</article><!--/.post-->	