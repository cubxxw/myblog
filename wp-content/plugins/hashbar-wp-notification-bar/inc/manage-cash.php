<?php

namespace Hashbarfree\Analytics; 

class Cash_Data {

    /**
     * [read]
     * @param  array  $args
     * @return [array] traking array
    */
    public static function cached(){
        global $wpdb;

        $order_by                  = 'totalviews';
        $table_name 	           = $wpdb->prefix . 'hthb_analytics';
        $sql_total_count           = "SELECT SUM(views) AS totalviews, SUM(clicks) AS totalclicks FROM $table_name WHERE %d";
        $sql_postwise_count        = "SELECT post_id, SUM(views) AS totalviews, SUM(clicks) AS totalclicks FROM $table_name WHERE %d GROUP BY post_id";
        $sql_caountrywise_count    = "SELECT country, SUM(views) AS totalviews, SUM(clicks) AS totalclicks FROM $table_name WHERE %d GROUP BY country ORDER BY $order_by DESC LIMIT 10";
        $total_traking_count       = get_transient( 'total_hthb_analytics_count' );
        $postwise_traking_count    = get_transient( 'postwise_hthb_analytics_count' );
        $countrywise_traking_count = get_transient( 'countrywise_hthb_analytics_count' );

        if ( false === $total_traking_count ) {
            $total_traking_query = $wpdb->prepare( $sql_total_count, 1 );
            $total_traking_count = $wpdb->get_results( $total_traking_query, ARRAY_A );
            set_transient( 'total_hthb_analytics_count', $total_traking_count );
        }

        if ( false === $postwise_traking_count ) {
            $postwise_traking_query = $wpdb->prepare( $sql_postwise_count, 1 );
            $postwise_traking_count = $wpdb->get_results( $postwise_traking_query, ARRAY_A );
            set_transient( 'postwise_hthb_analytics_count', $postwise_traking_count );
        }

        if ( false === $countrywise_traking_count ) {
            $countrywise_traking_query = $wpdb->prepare( $sql_caountrywise_count, 1 );
            $countrywise_traking_count = $wpdb->get_results( $countrywise_traking_query, ARRAY_A );
            set_transient( 'countrywise_hthb_analytics_count', $countrywise_traking_count );
        }
    }

    /**
     * [purge_cache] Manage Object Cache
     * @return [void] 
    */
    public static function delete_cache() { 	
        $get_total_count       = get_transient( 'total_hthb_analytics_count');
        $get_postwise_count    = get_transient( 'postwise_hthb_analytics_count');
    	$get_countrywise_count = get_transient( 'countrywise_hthb_analytics_count');

    	if ( false !== $get_total_count ) {
        	delete_transient('total_hthb_analytics_count');
    	}

        if ( false !== $get_postwise_count ) {
            delete_transient('postwise_hthb_analytics_count');
        }

        if ( false !== $get_countrywise_count ) {
            delete_transient('countrywise_hthb_analytics_count');
        }
    }

}