<?php

namespace Hashbarfree\Analytics;

/**
 * analytical data store
*/
class Analytical_Process 
{

    /**
     * [$_instance]
     * @var null
    */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Analytical_Process]
    */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	function __construct()
	{
        add_action( 'wp_ajax_hashbar_analytics', [ $this, 'analytics' ] );
        add_action( 'wp_ajax_nopriv_hashbar_analytics', [ $this, 'analytics' ] );
        add_action( 'wp_trash_post', [ $this, 'post_analytics_delete' ] );
	}

    public function analytics(){

        if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['id'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hashbar_analytics' ) ) {
            return;
        }

        global $user_ID;
        $country_info = wp_remote_get('http://ip-api.com/json/');
        if ( is_wp_error ( $country_info ) ) return;

        $country_info = json_decode( wp_remote_retrieve_body( $country_info ) );
        $country      = $country_info->country;
        $ip_address   = $_SERVER['REMOTE_ADDR'];

        if(!$country) return;

        $existing_post_analytics = $this->existing_post_analytics( $_POST['id'], $country, $ip_address );
        $existing_ip_analytics   = $this->existing_ip_analytics( $_POST['id'], $ip_address );
        $analytics_form          = hashbar_wpnb_get_opt('analytics_from');
        $should_count            = false;

        switch( $analytics_form ) {
            case 'everyone':
                $should_count = true;
                break;
            case 'guests':
                if( (int) $user_ID === 0 ) {
                    $should_count = true;
                }
                break;
            case 'registered_users':
                if( (int) $user_ID > 0 ) {
                    $should_count = true;
                }
                break;
        }

        if( $should_count === false || (hashbar_wpnb_get_opt('count_onece_byip')  && !empty($existing_ip_analytics)) ) {
            wp_die();
        }

        $data_analytics = [
            'post_id'    => $_POST['id'],
            'views'      => 'true' == $_POST['view'] ? 1 : 0,
            'clicks'     => 'true' == $_POST['clicked'] ? 1 : 0,
            'ip_address' => $ip_address,
            'country'    => $country,
        ];

        if(!empty($existing_post_analytics)){
            $this->update_analytics($data_analytics);
        }else{
            $this->store_analytics($data_analytics);
        }

        Cash_Data::delete_cache();
        Cash_Data::cached();
        die();
    }

    public function store_analytics($data_analytics){
        global $wpdb;

        $data  = [
            'post_id'         => $data_analytics['post_id'],
            'views'           => $data_analytics['views'],
            'clicks'          => $data_analytics['clicks'],
            'ip_address'      => $data_analytics['ip_address'],
            'country'         => $data_analytics['country'],
            'created_date'    => current_time( 'mysql' ),
            'updated_date'    => current_time( 'mysql' ),
        ];

        $table_name = $wpdb->prefix . 'hthb_analytics';

        $wpdb->insert( $table_name, $data );
    }

    public function existing_ip_analytics($post_id,$ip){
        global $wpdb;
        $post_id        = (int)$post_id;
        $table_name     = $wpdb->prefix . 'hthb_analytics';
        $sql            = "SELECT * FROM {$table_name} WHERE post_id = %d AND ip_address= %s";
        $query          = $wpdb->prepare( $sql,$post_id,$ip);
        $results        = $wpdb->get_results( $query,ARRAY_A);

        return $results;
    }

    public function existing_post_analytics($post_id,$country,$ip){
        global $wpdb;
        $post_id        = (int)$post_id;
        $table_name     = $wpdb->prefix . 'hthb_analytics';
        $sql            = "SELECT * FROM {$table_name} WHERE post_id = %d AND country = %s AND ip_address= %s";
        $query          = $wpdb->prepare( $sql,$post_id,$country,$ip);
        $results        = $wpdb->get_results( $query,ARRAY_A);

        return $results;
    }

    public function update_analytics($data){
        global $wpdb;
        $post_id             = (int)$data['post_id'];
        $table_name          = $wpdb->prefix . 'hthb_analytics';
        $existing_data       = $this->existing_post_analytics($post_id,$data['country'],$data['ip_address'])[0];
        
        $wpdb->update(
            $table_name,
            array( 
                'views'        => $existing_data['views']  + $data['views'], 
                'clicks'       => $existing_data['clicks'] + $data['clicks'], 
                'updated_date' => current_time( 'mysql' ), 
            ),
            array(
                'post_id'   => $post_id,
                'country'   => $data['country'],
                'ip_address'=> $data['ip_address'],
            )
        );
    }

    public function post_analytics_delete($post_id = ''){
        if ( isset( $_GET['post'] ) && is_array( $_GET['post'] ) ) {
            foreach ( $_GET['post'] as $post_id ) {
                $this->delete_analytics_data( $post_id );
            }
        } else {
            $this->delete_analytics_data( $post_id );
        }
    }

    private function delete_analytics_data($pid){
        global $wpdb;
        $table_name = $wpdb->prefix . 'hthb_analytics';

        $query = $wpdb->prepare( "SELECT post_id FROM $table_name WHERE post_id = %d", $pid );
        $var = $wpdb->get_var( $query );
        if ( $var ) {
            $query2 = $wpdb->prepare( "DELETE FROM $table_name WHERE post_id = %d", $pid );
            $wpdb->query( $query2 );
            Cash_Data::delete_cache();
            Cash_Data::cached();
        }
    }
      
}

Analytical_Process::instance();