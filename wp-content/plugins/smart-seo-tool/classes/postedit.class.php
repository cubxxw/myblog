<?php
/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com/
 */

class Smart_SEO_Tool_PostEdit {
	public function __construct() {}

	public static function cnf( $key, $default = null ) {
		return Smart_SEO_Tool_Admin::cnf($key, $default);
	}

	public static function init(){
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
			add_action( 'save_post', array( __CLASS__, 'save_post_meta' ) );
			add_action( 'admin_head-post.php', array( __CLASS__, 'admin_head' ) ,9);
			add_action( 'admin_head-post-new.php', array( __CLASS__, 'admin_head' ) ,9);
		}

		add_action( 'edit_post', array( __CLASS__, 'edit_post' ), 51, 2 );
	}

	public static function admin_head()
    {
        $active = self::cnf( 'tdk.active', 0 );
        if ( ! $active ) {
            return;
        }
        wp_enqueue_style( 'wbp-admin-style-sst', plugin_dir_url( SMART_SEO_TOOL_BASE_FILE ) . 'assets/wbp_admin.css', array(), SMART_SEO_TOOL_VERSION );
        wp_enqueue_script( 'wbp-admin-js-sst', plugin_dir_url( SMART_SEO_TOOL_BASE_FILE ) . 'assets/wb_admin_sst.js', array(), SMART_SEO_TOOL_VERSION, true );

    }

	public static function add_meta_box() {
		$active = self::cnf( 'tdk.active', 0 );
		if ( ! $active ) {
			return;
		}
		add_meta_box(
			'wbolt_meta_box_sst',
			'SEO信息设置',
			array( __CLASS__, 'render_meta_box' ),
			null,
			'advanced', 'high'
		);
	}

	public static function render_meta_box( $post ) {



		$meta_val = get_post_meta( $post->ID, 'wb_sst_seo', true );
		if ( ! $meta_val || ! is_array( $meta_val ) ) {
			$meta_val = array( 0 => '', 1 => '', 2 => '' );
		}
		$sst_opt                = array();
		$sst_opt['title']       = $meta_val[0];
		$sst_opt['keywords']    = $meta_val[1];
		$sst_opt['description'] = $meta_val[2];

		$inline_js = 'var wb_sst_opt={opt:' . json_encode( $sst_opt ) . ', admin_url:"'. admin_url() .'"};';
		wp_add_inline_script( 'wbp-admin-js-sst', $inline_js, 'before' );

		echo '<div id="WB_PostMetaBox_SST"></div>';
	}

	public static function  array_sanitize_text_field($value)
    {
        if(is_array($value)){
            foreach($value as $k=>$v){
                $value[$k] = self::array_sanitize_text_field($v);
            }
            return $value;
        }else{
            return sanitize_text_field($value);
        }
    }

	public static function save_post_meta( $post_id ) {
		if ( isset( $_POST['wb_sst_seo'] )  && is_array($_POST['wb_sst_seo'])) {
		    $data = self::array_sanitize_text_field($_POST['wb_sst_seo']);
			update_post_meta( $post_id, 'wb_sst_seo', $data );
		}
	}

	public static function edit_post( $post_id, $post ) {
		global $wpdb;

		$conf = self::cnf( 'broken' );

		if ( ! $conf['active'] ) {
			return;
		}
		if ( ! isset( $conf['post_type'] ) ) {
			$conf['post_type'] = array( 'post' );
		}
		if ( ! isset( $conf['post_status'] ) ) {
			$conf['post_status'] = array( 'publish', 'future', 'pending' );
		}
		if ( ! in_array( $post->post_type, $conf['post_type'] ) ) {
			return;
		}
		if ( ! in_array( $post->post_status, $conf['post_status'] ) ) {
			return;
		}
		$t = $wpdb->prefix . 'wb_sst_broken_url';

		$list = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $t WHERE post_id=%d", $post->ID ) );

		if ( empty( $post->post_content ) || ! preg_match_all( '#<a([^>]+)>(.+?)</a>#is', $post->post_content, $match ) ) {
			if ( $list ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM $t WHERE post_id=%d AND url_md5 IS NULL", $post->ID ) );
			} else {
				$d = array(
					'post_id'     => $post->ID,
					'create_date' => current_time( 'mysql' ),
					'memo'        => 'log'
				);
				$wpdb->insert( $t, $d );
			}

			return;
		}

		$exists_url = array();
		//empty log add marker
		if ( empty( $list ) ) {
			$d = array(
				'post_id'     => $post->ID,
				'create_date' => current_time( 'mysql' ),
				'memo'        => 'log'
			);
			$wpdb->insert( $t, $d );
		} else {
			foreach ( $list as $k => $v ) {
				if ( ! $v->url ) {
					continue;
				}
				$exists_url[ $v->url_md5 ] = $v->id;
			}
		}

		$host            = parse_url( home_url(), PHP_URL_HOST );
		$host_match_rule = array( preg_quote( $host ) );
		if ( $conf['exclude'] ) {
			foreach ( $conf['exclude'] as $v ) {
				$host_match_rule[] = preg_quote( $v );
			}
		}
		if ( $host_match_rule ) {
			$host_match_rule = implode( '|', $host_match_rule );
		}

		$same_url = array();
		foreach ( $match[1] as $k => $a_html ) {
			if ( ! preg_match( '#href=("|\')(.+?)("|\')#is', $a_html, $a_match ) ) {
				continue;
			}
			$url = $a_match[2];
			if ( ! preg_match( '#^https?://([^/]+)#is', $url, $host_match ) ) {
				continue;
			}
			if ( $host_match_rule && preg_match( '#(' . $host_match_rule . ')#i', $host_match[1] ) ) {
				continue;
			}


			$text = trim( strip_tags( $match[2][ $k ] ) );
			if ( ! $text && preg_match( '#<img#i', $match[1][ $k ] ) ) {
				$text = 'img';
			}

			$d = array(
				'post_id'     => $post->ID,
				'create_date' => current_time( 'mysql' ),
				'url'         => $url,
				'url_md5'     => md5( $url ),
				'url_text'    => $text
			);

			if ( isset( $same_url[ $d['url_md5'] ] ) ) {
				continue;
			}
			$same_url[ $d['url_md5'] ] = 1;


			if ( $exists_url && isset( $exists_url[ $d['url_md5'] ] ) ) {
				unset( $exists_url[ $d['url_md5'] ] );
				continue;
			}

			$wpdb->insert( $t, $d );
		}
		if ( ! empty( $exists_url ) ) {
			$sid = implode( ',', $exists_url );
			$wpdb->query( "DELETE FROM $t WHERE id IN($sid)" );
		}
	}

}