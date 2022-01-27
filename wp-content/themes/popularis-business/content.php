<article class="col-md-6">
    <div <?php post_class(); ?>>                    
        <div class="post-item row">
            <div class="news-thumb col-md-12">
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                    <?php the_post_thumbnail('popularis-img'); ?>
                </a>
            </div>
            <div class="news-text-wrap col-md-12">
                <span class="posted-date">
                    <?php echo esc_html(get_the_date()); ?>
                </span>    
                <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
                <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                </div><!-- .post-excerpt -->
            </div><!-- .news-text-wrap -->

        </div><!-- .post-item -->
    </div>
</article>
