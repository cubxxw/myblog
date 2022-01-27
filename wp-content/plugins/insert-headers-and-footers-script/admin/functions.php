<?php

// add condition column
add_filter( 'manage_ihafs_script_posts_columns', 'ihafs_filter_posts_columns' );
function ihafs_filter_posts_columns( $columns ) {

  $columns = array(
    'cb' => $columns['cb'],
    'title' => $columns['title'],
    'condition' => __( 'Conditon', 'ihafs' ),
    'date' => $columns['date'],
  );

  return $columns;
}


// condtion column value
add_action( 'manage_ihafs_script_posts_custom_column', 'ihafs_posts_custom_column', 10, 2);
function ihafs_posts_custom_column( $column, $post_id ) {
  if ( 'condition' === $column ) {
    echo ucwords(get_post_meta( $post_id, '_ihafs_condition', true ));
  }
}