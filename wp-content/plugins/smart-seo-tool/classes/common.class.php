<?php
/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com/
 */

class Smart_SEO_Tool_Common {
	public static $seo_info = array();

	public static function cnf( $key, $default = null ) {
		return Smart_SEO_Tool_Admin::cnf($key, $default);
	}

	public function __construct() { }

	public static function init(){

	    global $wp_version;

		if ( !is_admin() ) {
			$active = self::cnf( 'tdk.active', 0 );
			//print_r([$active]);
			if ( $active ) {

				add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ) );
                add_filter('wp_robots',array(__CLASS__,'wp_robots'));
				if(version_compare($wp_version,'5.7')<0){
                    //add_filter('wp_robots',array(__CLASS__,'wp_robots'));
                    add_action( 'wp_head', array(__CLASS__,'wb_sseot_robots'), 1 );
                }


			}

			if ( self::cnf( 'img_seo.active', false ) ) {
				new Smart_SEO_Tool_Images();
			}

            if(self::cnf('robots_seo.active')){
                add_filter('robots_txt',array(__CLASS__,'robots_txt'),10,2);
            }

            $spe = self::cnf('tdk.separator');
            if($spe){
                add_filter('document_title_separator',function($sep){
                    return self::cnf('tdk.separator');
                },100);
            }
		}


		add_action( 'wb_smart_seo_tool_cron', array( __CLASS__, 'wb_smart_seo_tool_cron' ) );

		if ( ! wp_next_scheduled( 'wb_smart_seo_tool_cron' ) ) {
			wp_schedule_event( strtotime( current_time( 'Y-m-d H:i:s', 1 ) ), 'hourly', 'wb_smart_seo_tool_cron' );
		}


		add_filter('wb_seo_info',array(__CLASS__,'wb_seo_info_parse_empty'));
	}

    /**
     * 兼容5.7以下 wp_robots
     */
	public static function wb_sseot_robots()
    {
        $robots = apply_filters( 'wp_robots', array() );

        // Don't allow mutually exclusive directives.
        if ( ! empty( $robots['follow'] ) ) {
            unset( $robots['nofollow'] );
        }
        if ( ! empty( $robots['nofollow'] ) ) {
            unset( $robots['follow'] );
        }
        if ( ! empty( $robots['archive'] ) ) {
            unset( $robots['noarchive'] );
        }
        if ( ! empty( $robots['noarchive'] ) ) {
            unset( $robots['archive'] );
        }

        $robots_strings = array();
        foreach ( $robots as $directive => $value ) {
            if ( is_string( $value ) ) {
                // If a string value, include it as value for the directive.
                $robots_strings[] = "{$directive}:{$value}";
            } elseif ( $value ) {
                // Otherwise, include the directive if it is truthy.
                $robots_strings[] = $directive;
            }
        }

        if ( empty( $robots_strings ) ) {
            return;
        }

        echo "<meta name='robots' content='" . esc_attr( implode( ', ', $robots_strings ) ) . "' />\n";
    }

	public static function wp_robots($robots)
    {

        if(!get_option( 'blog_public' )){
            return $robots;
        }

        $noindex = Smart_SEO_Tool_Admin::cnf('tdk.noindex');
        if(empty($noindex) || !is_array($noindex)){
            return $robots;
        }
        $type = '';
        if ( is_author() ) {
            $type = 'author';
        } else if ( is_search() ) {
            $type = 'search';
        } else if ( self::is_tag() ) {
            $type = 'tag';
        } else if ( is_category() || is_archive() ) {
            $type = 'category';
        } else if ( is_single() || is_page() || is_singular() ) {
            $type = 'post';
            if(is_page()){
                $type = 'page';
            }
        }

        if(!$type){
            return $robots;
        }
        if(in_array($type,$noindex)){
            $robots['noindex'] = 1;
            $robots['nofollow'] = 1;
        }else{
            $robots['index'] = 1;
            $robots['follow'] = 1;
        }
        return $robots;
    }
	public static function robots_txt($output, $public){
        if ( $public ) {
            $robots_txt = self::cnf('robots_seo.content');
            if($robots_txt){
                return trim($robots_txt).PHP_EOL;
            }
        }
        return $output;
    }

	public static function wb_smart_seo_tool_cron() {
        $conf = self::cnf( 'broken' );
        if ( ! $conf['active'] ) {
            return;
        }

		self::scan_post();

		self::detect_broken_url();
	}

	public static function detect_broken_url() {
		global $wpdb;
		$conf = self::cnf( 'broken' );

		/*if ( ! $conf['active'] ) {
			return;
		}*/
		$day = absint( $conf['test_rate'] );
		if ( ! $day ) {
			$day = 30;
		}
		$t    = $wpdb->prefix . 'wb_sst_broken_url';
		$list = $wpdb->get_results( "SELECT * FROM $t WHERE url_md5 IS NOT NULL AND (check_date IS NULL OR DATE_ADD(check_date ,INTERVAL $day DAY) < NOW() ) LIMIT 10 " );

		foreach ( $list as $r ) {
			self::detect_url( $r->id, $r );
		}
	}

	public static function scan_post() {
		global $wpdb;

		$conf = self::cnf( 'broken' );

		/*if ( ! $conf['active'] ) {
			return;
		}*/
		if ( ! isset( $conf['post_type'] ) ) {
			$conf['post_type'] = array( 'post' );
		}
		if ( ! isset( $conf['post_status'] ) ) {
			$conf['post_status'] = array( 'publish', 'future', 'pending' );
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

		$t     = $wpdb->prefix . 'wb_sst_broken_url';
		$where = " AND NOT EXISTS(SELECT id FROM $t WHERE $t.post_id=$wpdb->posts.ID) ";
		$list  = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type IN('" . implode( "','", $conf['post_type'] ) . "') AND post_status IN('" . implode( "','", $conf['post_status'] ) . "') $where ORDER BY post_date DESC LIMIT 100" );

		foreach ( $list as $post ) {
			$d = array(
				'post_id'     => $post->ID,
				'create_date' => current_time( 'mysql' ),
				'memo'        => 'log'
			);
			$wpdb->insert( $t, $d );

			if ( empty( $post->post_content ) || ! preg_match_all( '#<a([^>]+)>(.+?)</a>#is', $post->post_content, $match ) ) {
				continue;
			}

			$exists_url = array();
			foreach ( $match[1] as $k => $a_html ) {
				if ( ! preg_match( '#href=("|\')(.+?)("|\')#is', $a_html, $a_match ) ) {
					continue;
				}
				$url = trim( $a_match[2] );
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
				if ( isset( $exists_url[ $d['url_md5'] ] ) ) {
					continue;
				}
				$exists_url[ $d['url_md5'] ] = 1;


				$wpdb->insert( $t, $d );
			}


		}

	}

	public static function clear_broken_url() {
		global $wpdb;
		$conf = self::cnf( 'broken' );


		/*if ( ! $conf['active'] ) {
			return false;
		}*/

		$t = $wpdb->prefix . 'wb_sst_broken_url';

		$wpdb->query( "TRUNCATE $t" );

		return true;
	}

	public static function broken_url_count() {
		global $wpdb;
		$conf = self::cnf( 'broken' );

		$sum = array( 'error' => 0, 'redirect' => 0, 'ok' => 0, 'other' => 0 );

		/*if ( ! $conf['active'] ) {
			return $sum;
		}*/
		$t    = $wpdb->prefix . 'wb_sst_broken_url';
		$list = $wpdb->get_results( "SELECT COUNT(1) num,`code` FROM $t WHERE `code` IS NOT NULL AND url_md5 IS NOT NULL GROUP BY `code` " );
		foreach ( $list as $r ) {
			if ( preg_match( '#^30#', $r->code ) ) {
				$sum['redirect'] += $r->num;
			} else if ( preg_match( '#^(4|5|e)#', $r->code ) ) {
				$sum['error'] += $r->num;
			} else if ( preg_match( '#^2#', $r->code ) ) {
				$sum['ok'] += $r->num;
			} else {
				$sum['other'] += $r->num;
			}
		}

		return $sum;

	}

	public static function mark_broken_url( $id, $r = null ) {
		global $wpdb;
		$conf = self::cnf( 'broken' );

		/*if ( ! $conf['active'] ) {
			return null;
		}*/
		$t = $wpdb->prefix . 'wb_sst_broken_url';
		if ( ! $r ) {
			$r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id=%d", $id ) );
		}
		if ( ! $r ) {
			return null;
		}
		$wpdb->update( $t, array(
			'code'       => 200,
			'check_date' => '2023-10-01 10:00:00',
			'memo'       => 'mark as ok'
		), array( 'id' => $r->id ) );

		return true;
	}

	public static function remove_broken_url( $id, $r = null ) {
		global $wpdb;
		$conf = self::cnf( 'broken' );

		/*if ( ! $conf['active'] ) {
			return null;
		}*/
		$t = $wpdb->prefix . 'wb_sst_broken_url';
		if ( ! $r ) {
			$r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id=%d", $id ) );
		}
		if ( ! $r ) {
			return null;
		}
		$wpdb->delete( $t, array( 'id' => $r->id ) );
		$post = get_post( $r->post_id );
		if ( ! $post ) {
			return null;
		}
		if ( ! preg_match_all( '#<a[^>]+>(.+?)</a>#is', $post->post_content, $match ) ) {
			return null;
		}
		$content = $post->post_content;
		$change  = 0;
		foreach ( $match[0] as $k => $a_html ) {
			if ( ! preg_match( '#href=("|\')(.+?)("|\')#is', $a_html, $a_match ) ) {
				continue;
			}
			$url = trim( $a_match[2] );
			if ( $r->url_md5 == md5( $url ) ) {
				$content = str_replace( $a_html, $match[1][ $k ], $content );
				$change  = 1;
			}
		}
		if ( $change ) {
			wp_update_post( array( 'ID' => $post->ID, 'post_content' => $content ) );
		}

		return true;
	}

	public static function detect_url( $id, $r = null ) {
		global $wpdb;
		$conf = self::cnf( 'broken' );

		/*if ( ! $conf['active'] ) {
			return null;
		}*/
		$t = $wpdb->prefix . 'wb_sst_broken_url';
		if ( ! $r ) {
			$r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id=%d", $id ) );
		}

		if ( ! $r ) {
			return null;
		}

		$d    = array( 'check_date' => current_time( 'mysql' ) );
		$http = wp_remote_head( $r->url, array( 'timeout' => 30, 'sslverify' => false ) );
		if ( is_wp_error( $http ) ) {
			$d['code'] = 'error-1';
			$d['memo'] = $http->get_error_message();
		} else {
			//$code = wp_remote_retrieve_header($http,'Location');
			$code      = wp_remote_retrieve_response_code( $http );
			$d['code'] = $code;
			$d['memo'] = null;
			$url_base  = '';
			if ( preg_match( '#^https?://[^/]+#', $r->url, $base_match ) ) {
				$url_base = $base_match[0];
			}
			if ( preg_match( '#^30#', $code ) ) {

				$num = 0;
				do {
					$redirect = wp_remote_retrieve_header( $http, 'location' );
					if ( ! $redirect ) {
						break;
					}
					if ( ! preg_match( '#^https?://#', $redirect ) && preg_match( '#^/#', $redirect ) ) {
						$redirect = $url_base . $redirect;
					}
					if ( preg_match( '#^https?://[^/]+#', $redirect, $base_match ) ) {
						$url_base = $base_match[0];
					}
					$http = wp_remote_head( $redirect, array( 'timeout' => 30, 'sslverify' => false ) );
					if ( is_wp_error( $http ) ) {
						$d['code'] = 'error-2';
						$d['memo'] = $http->get_error_message();
						break;
					}
					$code = wp_remote_retrieve_response_code( $http );
					if ( ! preg_match( '#^30#', $code ) ) {
						if ( $code == 200 ) {
							$d['memo'] = $redirect;
						} else if ( preg_match( '#^(4|5)#', $code ) ) {
							$d['code'] = $code;
						} else {
							$d['code'] = 'error-3';
							$d['memo'] = $code;
						}
						//$d['code'] = $code;
						break;
					}
					$num ++;
				} while ( $num < 6 );
			}

		}

		$r->code = $d['code'];
		$r->memo = $d['memo'];
		$wpdb->update( $t, $d, array( 'id' => $r->id ) );

		return $r;

	}

	public static function render_title_tag() {
		/*if ( ! current_theme_supports( 'title-tag' ) ) {
            return;
        }*/
		echo '<title>' . str_replace( '&#8211;', '-', wp_get_document_title() ) . '</title>' . "\n";
	}

	public static function wp_title( $title, $sep, $seplocation ) {

		return str_replace( '&#8211;', '-', wp_get_document_title() );

	}

	public static function wp_title_parts( $part ) {
		return $part;
	}

	public static function document_title_parts( $part ) {
		//print_r($part);
		//print_r(self::$seo_info);
		if ( isset( self::$seo_info['title'] ) && self::$seo_info['title'] ) {
			$part['title'] = self::$seo_info['title'];
		}
		if ( ( is_home() || is_front_page() ) && isset( $part['tagline'] ) ) {
			unset( $part['tagline'] );
		}
		if(isset($part['site'])){
		    unset($part['site']);
        }

		return $part;
	}

	public static function wp_head_seo() {
		if ( isset( self::$seo_info['kw'] ) && self::$seo_info['kw'] ) {
			echo sprintf( '<meta name="keywords" content="%s" />' . "\n", esc_attr(self::$seo_info['kw']) );
		}
		if ( isset( self::$seo_info['desc'] ) && self::$seo_info['desc'] ) {
			echo sprintf( '<meta name="description" content="%s" />' . "\n", esc_attr(self::$seo_info['desc']) );
		}

	}

	public static function theme_has_title() {
		$dir = get_template_directory();

		$key = 'wb_sst_' . md5( $dir );
		$val = get_option( $key, null );
		if ( null !== $val ) {
			return $val;
		}
		$val         = 0;
		$header_file = $dir . '/header.php';
		if ( file_exists( $header_file ) ) {
			$content = file_get_contents( $header_file );
			if ( preg_match( '#<title>.+?</title>#is', $content ) ) {
				$val = 1;
			}
		}
		update_option( $key, $val, true );

		return $val;
	}

	public static function template_redirect() {

		$info           = self::seo_info();
		self::$seo_info = $info;
		//print_r($info);

		//7b2
		if ( function_exists( 'zrz_seo_head_meta' ) ) {
			remove_action( 'wp_head', 'zrz_seo_head_meta' );
			remove_filter( "document_title_parts", "zrz_seo_document_title" );
		}

		if ( $info['title'] ) {
			add_filter( 'document_title_parts', array( __CLASS__, 'document_title_parts' ) );
			if ( self::theme_has_title() ) {
				add_filter( 'wp_title', array( __CLASS__, 'wp_title' ), 100, 3 );
				//dux
				if ( defined( 'THEME_VERSION' ) && function_exists( '_title' ) ) {
					$GLOBALS['new_title'] = str_replace( '&#8211;', '-', wp_get_document_title() );
				}
			} else if ( current_theme_supports( 'title-tag' ) ) {
				remove_action( 'wp_head', '_wp_render_title_tag', 1 );
				add_action( 'wp_head', array( __CLASS__, 'render_title_tag' ), 1 );
			} else {
				add_action( 'wp_head', array( __CLASS__, 'render_title_tag' ), 1 );
			}
		}

		if ( $info['kw'] || $info['desc'] ) {
			add_action( 'wp_head', array( __CLASS__, 'wp_head_seo' ), 1 );
		}


	}

	public static function term_category() {
		global $wp_query, $wp_taxonomies;


		if ( ! $wp_taxonomies ) {
			return array();
		}
		$ret = array();
		//print_r($wp_taxonomies);
		foreach ( $wp_taxonomies as $taxonomy ) {

			if ( ! $taxonomy->public || ! $taxonomy->hierarchical || ! preg_match( '#categor#', $taxonomy->meta_box_cb ) ) {//
				continue;
			}
			//print_r($taxonomy);
			$ret[] = $taxonomy;
		}

		return $ret;
	}

	public static function is_tag() {
		global $wp_query, $wp_taxonomies;
		if ( is_tag() ) {
			return true;
		}
		$object = get_queried_object();

		if ( ! $object || ! ( $object instanceof WP_Term ) ) {
			return false;
		}
		if ( ! $wp_taxonomies || ! isset( $wp_taxonomies[ $object->taxonomy ] ) ) {
			return false;
		}
		$taxonomy = $wp_taxonomies[ $object->taxonomy ];
		if ( ! $taxonomy->hierarchical && preg_match( '#tag#', $taxonomy->meta_box_cb ) ) {
			return true;
		}

		return false;
	}

	public static function parse_tpl($tpl,$data,$type='t')
    {
        $common = [];
        $common['site_title'] = get_option( 'blogname' );
        $common['site_subtitle'] = get_option( 'blogdescription' );
        $common['separator'] = apply_filters( 'document_title_separator', self::cnf( 'tdk.separator') );

        if($data && is_array($data)){
            $common = $data + $common;
        }

        foreach($common as $k=>$v){
            $tpl = str_replace('%'.$k.'%',$v,$tpl);
        }

        return $tpl;
    }

    public static function wb_seo_info_parse_empty($info){
	    /*if(isset($info['title']) && $info['title']){
	        $sp = self::cnf( 'tdk.separator','-');
            $title = explode($sp,$info['title']);
            $title = array_filter($title,'trim');
            $title = array_unique($title);
            $info['title'] = implode($sp,$title);
        }*/
	    if(isset($info['kw']) && $info['kw']){
	        $kw = explode(',',$info['kw']);
	        $kw = array_filter($kw,'trim');
	        $kw = array_unique($kw);
	        $info['kw'] = implode(',',$kw);
        }

	    return $info;
    }

	public static function seo_info() {


		$title = '';//wp_get_document_title();
		$kw    = $desc = '';
		if ( is_home() || is_front_page() ) {

			$meta = self::cnf( 'tdk.index', array( '', '', '' ) );
			$data = [];
			if ( isset( $meta[0] ) && $meta[0] ) {
				$title = self::parse_tpl($meta[0],$data,'t');
			}
			if ( isset( $meta[1] ) && $meta[1] ) {
				$kw = self::parse_tpl($meta[1],$data,'k');
			}
			if ( isset( $meta[2] ) && $meta[2] ) {
				$desc = self::parse_tpl($meta[2],$data,'d');
			}

		} else if ( is_author() ) {

			/* 标题: 「{author_name}」作者主页 - {sitename}
               关键词: 读取该作者所有文章Top5热门关键词，以英文逗号分隔
               描述:  「{author_name}」作者主页， 「{author_name}」主要负责{该作者所有文章Top5热门关键词（以顿号分割）}等内容发布。
               注：{author_name}指作者昵称
             */

			global $authordata, $wpdb;

            $meta = self::cnf('tdk.author',['','','']);
            if(!is_array($meta)){
                $meta = ['','',''];
            }
            $title = $kw = $desc = '';
            if(isset($meta[0]) && $meta[0]){
                $title = trim($meta[0]);
            }
            if(!$title){
                $title = '%author_name%作者主页%separator%%site_title%';
            }
            if(isset($meta[1]) && $meta[1]){
                $kw = trim($meta[1]);
            }
            if(!$kw){
                $kw = '%author_name%,%list_keywords%';
            }
            if(isset($meta[2]) && $meta[2]){
                $desc = trim($meta[2]);
            }
            if(!$desc){
                $desc = '%author_name%作者主页，主要负责%list_keywords%等内容发布。';
            }
            $data = [];
            $data['author_name'] = get_the_author();
			//$sep = apply_filters('document_title_separator', '-');
			//$title = implode($sep, array('「'.get_the_author().'」作者主页', get_bloginfo('name', 'display')));
			//$title = self::formatTitle($title);
			//$title = '「' . get_the_author() . '」作者主页';
            $list_keywords = 0;
            if(preg_match('#%list_keywords%#',implode('',[$title,$kw,$desc]))){
                $list_keywords = 1;
            }
			if ( $list_keywords && is_object( $authordata ) ) {

				$top_words = get_user_meta( $authordata->ID, 'seo_top_keywords', true );
				$time      = current_time( 'timestamp' );
				if ( ! $top_words || $top_words['time'] < $time ) {

					$sql = "SELECT c.`term_taxonomy_id`,c.term_id,COUNT(1) num FROM $wpdb->posts a,$wpdb->term_relationships r,$wpdb->term_taxonomy c ";
					$sql .= " WHERE a.post_author=%d AND a.post_status='publish' AND a.post_type='post' AND a.ID=r.object_id AND r.term_taxonomy_id=c.term_taxonomy_id AND c.taxonomy='post_tag'";
					$sql .= " GROUP BY c.`term_taxonomy_id` ORDER by num DESC LIMIT 3";

					$sql = "SELECT t.name from $wpdb->terms t,($sql) tt WHERE t.term_id=tt.term_id";

					$sql = $wpdb->prepare( $sql, $authordata->ID );

					$col = $wpdb->get_col( $sql );

					$top_words = array( 'time' => $time + WEEK_IN_SECONDS, 'keywords' => $col );
					update_user_meta( $authordata->ID, 'seo_top_keywords', $top_words );
				}

				if ( $top_words['keywords'] ) {
				    $data['list_keywords'] = implode(',',$top_words['keywords']);
					//$tpls[1] = '<meta name="keywords" content="%s" />';
					//$kw = implode( ',', $top_words['keywords'] );
					//$tpls[2] = '<meta name="description" content="%s" />';
					//$desc = '「' . get_the_author() . '」作者主页，主要负责' . implode( '、', $top_words['keywords'] ) . '等内容发布。';
				}

			}

            if($title){
                $title = self::parse_tpl($title,$data,'t');
            }
            if($kw){
                $kw = self::parse_tpl($kw,$data,'k');
            }
            if($desc){
                $desc = self::parse_tpl($desc,$data,'d');
            }



		} else if ( is_search() ) {


			//$q = get_queried_object();
			//print_r($q);
			global $wp_query, $wpdb;


            $meta = self::cnf('tdk.search',['','','']);
            if(!is_array($meta)){
                $meta = ['','',''];
            }
            $title = $kw = $desc = '';
            if(isset($meta[0]) && $meta[0]){
                $title = trim($meta[0]);
            }
            if(!$title){
                $title = '与%search_keyword%匹配搜索结果%separator%%site_title%';
            }
            if(isset($meta[1]) && $meta[1]){
                $kw = trim($meta[1]);
            }
            if(!$kw){
                $kw = '%search_keyword%,%list_keywords%';
            }
            if(isset($meta[2]) && $meta[2]){
                $desc = trim($meta[2]);
            }
            if(!$desc){
                $desc = '当前页面展示所有与%search_keyword%相关的匹配结果，包括%list_keywords%等内容。';
            }
            $data = [];
            $data['search_keyword'] = get_search_query( false );
            $list_keywords = 0;
            if(preg_match('#%list_keywords%#',implode('',[$title,$kw,$desc]))){
                $list_keywords = 1;
            }
			/*
            标题: 与「{search_keyword}」匹配搜索结果 - {sitename}
            关键词: {search_keyword}, {search_keyword}相关, {search_keyword}内容, 搜索结果所有文章Top5热门关键词
            描述: 当前页面展示所有与「{search_keyword}」相关的匹配结果，包括搜索结果文章Top5关键词（以顿号分割）等内容。
            注：{search_keyword}指访客搜索关键词
            */
			//$sep = apply_filters('document_title_separator', '-');

			//$title = implode($sep, array('与「'.$q_kw.'」匹配的搜索结果', get_bloginfo('name', 'display')));
			//$title = self::formatTitle($title);
			//$title = '与「' . $q_kw . '」匹配的搜索结果';

			//$kws = array( $q_kw, $q_kw . '相关', $q_kw . '内容' );//array_merge(,$top_words['keywords']);
			//$tpls[1] = '<meta name="keywords" content="%s" />';
			//$kw = implode( ',', $kws );
			//$tpls[2] = '<meta name="description" content="%s" />';
			//$desc = '当前页面展示所有与「' . $q_kw . '」搜索词相匹配的结果';//.implode('、',$top_words['keywords']);

			if ($list_keywords &&  $wp_query->found_posts ) {
				$post_ids = array();
				foreach ( $wp_query->posts as $p ) {
					$post_ids[] = $p->ID;
				}
				//print_r($wp_query);

				$post_ids = implode( ',', $post_ids );

				$sql = "SELECT tt.term_id,tt.term_taxonomy_id,count(1) num FROM $wpdb->term_relationships r , $wpdb->term_taxonomy tt,$wpdb->terms t where r.object_id IN($post_ids) AND r.term_taxonomy_id=tt.term_taxonomy_id AND tt.taxonomy<>'category' group by tt.term_taxonomy_id order by num DESC LIMIT 3";

				$sql = "SELECT t.name FROM $wpdb->terms t ,($sql) tmp where  tmp.term_id=t.term_id ";


				//echo $sql;
				$col = $wpdb->get_col( $sql );
				if ( $col ) {

				    $data['list_keywords'] = implode(',',$col);
					//$kw   .= ',' . implode( ',', $col );
					//$desc .= ',包括' . implode( '、', $col ) . '等内容。';
				}

			}

            if($title){
                $title = self::parse_tpl($title,$data,'t');
            }
            if($kw){
                $kw = self::parse_tpl($kw,$data,'k');
            }
            if($desc){
                $desc = self::parse_tpl($desc,$data,'d');
            }

		} else if ( self::is_tag() ) {

            $meta = self::cnf('tdk.tag',['','','']);
            if(!is_array($meta)){
                $meta = ['','',''];
            }
            $title = $kw = $desc = '';
            if(isset($meta[0]) && $meta[0]){
                $title = trim($meta[0]);
            }
            if(!$title){
                $title = '%tag_name%相关文章列表%separator%%site_title%';
            }
            if(isset($meta[1]) && $meta[1]){
                $kw = trim($meta[1]);
            }
            if(!$kw){
                $kw = '%tag_name%,%list_keywords%';
            }
            if(isset($meta[2]) && $meta[2]){
                $desc = trim($meta[2]);
            }
            if(!$desc){
                $desc = '关于%tag_name%相关内容全站索引列表，包括%list_keywords%等内容。';
            }

            $data = [];

            global $wpdb;

			$tag = get_queried_object();
			//print_r($tag);

			//print_r($tag);
			/* 标题: 「{tag}」相关文章列表 - 站点名称
             关键词: {tag}, {tag}相关, {tag}内容及标签结果文章Top5关键词
             描述: 关于「{tag}」相关内容全站索引列表，包括标签列表页所有结果Top5关键词（以顿号分割）。
             注：{tag}指文章编辑时输入的标签词语*/


			//$sep = apply_filters('document_title_separator', '-');
			//$title = implode($sep, array('「'.$tag->name.'」相关文章列表', get_bloginfo('name', 'display')));
			//$title = self::formatTitle($title);

			//$title = '「' . $tag->name . '」相关文章列表';


            $data['tag_name'] = $tag->name;
            $data['list_keywords'] = '';
            $data['description'] = $tag->description;
            $list_keywords = 0;
            if(preg_match('#%list_keywords%#',implode('',[$title,$kw,$desc]))){
                $list_keywords = 1;
            }
            if($list_keywords){

                $top_words = get_term_meta( $tag->term_id, 'seo_top_keywords', true );
                $time      = current_time( 'timestamp' );
                if ( ! $top_words || $top_words['time'] < $time ) {
                    //tag 下的所有文章
                    //$sql = "SELECT p.ID  FROM $wpdb->term_relationships r ,$wpdb->posts p WHERE r.term_taxonomy_id = %d and  r.object_id=p.ID AND p.post_status='publish'";

                    //所有文章下的tag，取数量前五
                    //$sql = "SELECT tt.term_taxonomy_id,tt.term_id,COUNT(1) FROM $wpdb->term_taxonomy tt ,$wpdb->term_relationships rr ,$wpdb->posts pp WHERE tt.term_taxonomy_id=rr.term_taxonomy_id  AND tt.taxonomy<>'category' AND rr.object_id=pp.ID AND pp.ID IN($sql)";
                    //$sql .= " GROUP BY tt.term_taxonomy_id ORDER BY tt.count DESC LIMIT 3";


                    $sql = "SELECT COUNT(1) num,c.term_taxonomy_id,d.term_id
                            FROM $wpdb->term_relationships a,$wpdb->posts b,$wpdb->term_relationships c,$wpdb->term_taxonomy d 
                            WHERE a.`term_taxonomy_id`=%d and a.`object_id` = b.ID AND b.post_status='publish' 
                                AND c.object_id=b.ID AND c.term_taxonomy_id<>%d AND c.term_taxonomy_id=d.term_taxonomy_id AND d.taxonomy=%s
                            GROUP BY c.`term_taxonomy_id` ORDER BY num DESC LIMIT 3";

                    $sql = "SELECT t.name FROM $wpdb->terms t,($sql) tmp WHERE t.term_id=tmp.term_id ";

                    $sql = $wpdb->prepare( $sql, $tag->term_taxonomy_id,$tag->term_taxonomy_id,$tag->taxonomy );

                    //echo $sql;exit();
                    $col = $wpdb->get_col( $sql );

                    $top_words = array( 'time' => $time + WEEK_IN_SECONDS, 'keywords' => $col );
                    update_term_meta( $tag->term_id, 'seo_top_keywords', $top_words );
                }

                if ( $top_words['keywords'] ) {
                    //$kws = array_merge( array( $tag->name, $tag->name . '相关', $tag->name . '内容' ), $top_words['keywords'] );
                    //$tpls[1] = '<meta name="keywords" content="%s" />';
                    //$kw = implode( ',', $kws );
                    //$tpls[2] = '<meta name="description" content="%s" />';
                    //$desc = '关于「' . $tag->name . '」相关内容全站索引列表，包括' . implode( '、', $top_words['keywords'] ) . '。';
                    $data['list_keywords'] = implode(',',$top_words['keywords']);
                }

            }


			if($title){
			    $title = self::parse_tpl($title,$data,'t');
            }
			if($kw){
			    $kw = self::parse_tpl($kw,$data,'k');
            }
			if($desc){
			    $desc = self::parse_tpl($desc,$data,'d');
            }

		} else if ( is_category() || is_archive() ) {
            global $wpdb,$wp_query;


            $mode = self::cnf( 'tdk.category_mode');
            $meta = array( '', '', '' );
            $data = [];

            $data['cat_name'] = '';
            $data['parent_cat'] = '';
            $data['description'] = '';
            $data['list_keywords'] = '';
            $term = get_queried_object();

            do{
                if($mode == 1){
                    $meta = self::cnf('tdk.term_base',['','','']);
                    if(!$meta[0]){
                        $meta[0] = '%cat_name%%separator%%site_title%';
                    }
                    if(!$meta[2]){
                        $meta[2] = '%description%';
                    }
                    break;
                }

                //$term = get_queried_object();
                //print_r($term);
                if ( $term instanceof WP_Post_Type ) {
                    global $wp_taxonomies;
                    foreach ( $wp_taxonomies as $taxonomy ) {
                        //print_r($taxonomy);
                        if ( $taxonomy->public && $taxonomy->hierarchical && preg_match( '#categor#', $taxonomy->meta_box_cb ) ) {
                            if ( $taxonomy->object_type && in_array( $term->name, $taxonomy->object_type ) ) {
                                $meta = self::cnf( 'tdk.'.$taxonomy->name . '_index', array( '', '', '' ) );
                                break;
                            }
                        }
                    }
                } else if ( $term instanceof WP_Term ) {
                    $cid  = $term->term_id;
                    $meta = self::cnf( 'tdk.'.$cid, array( '', '', '' ) );
                } else if ( $term instanceof WP_Taxonomy ) {
                    $meta = self::cnf( 'tdk.'.$term->name . '_index', array( '', '', '' ) );
                }
                //print_r($meta);
            }while(0);

            $query_term_id = '';
            $query_taxonomy = 'category';
            //print_r($term);
            if($term){
                if ( $term instanceof WP_Post_Type ) {
                    global $wp_taxonomies;
                    $data['cat_name'] = $term->label;
                    $data['description'] = $term->description;
                    foreach($wp_taxonomies as $tax){
                        if(in_array($term->name,$tax->object_type)){
                            $query_taxonomy = $tax->name;
                            break;
                        }
                    }
                    //print_r(get_post_type_object($term->name));
                    //print_r(get_taxonomies(['_builtin'=>1]));
                    ///print_r([$wp_taxonomies]);

                } else if ( $term instanceof WP_Term ) {
                    $data['description'] = $term->description;
                    $data['cat_name'] = $term->name;
                    $query_term_id = $term->term_id;
                    $query_taxonomy = $term->taxonomy;
                    if(preg_match('#%parent_cat%#',implode('',$meta))){
                        //get_term_parents_list();
                        $parent_cat = [];
                        $parents = get_ancestors( $term->term_id,$term->taxonomy, 'taxonomy' );
                        foreach ( array_reverse( $parents ) as $term_id ) {
                            $parent = get_term($term_id, $term->taxonomy);
                            $parent_cat[] = $parent->name;
                        }
                        if($parent_cat){
                            $data['parent_cat'] = implode('/',$parent_cat);
                        }
                    }

                } else if ( $term instanceof WP_Taxonomy ) {
                    $data['cat_name'] = $term->label;
                    $data['description'] = $term->description;
                    $query_taxonomy = $term->name;
                }
            }else if ( is_year() ) {
                $data['cat_name'] = get_the_date( _x( 'Y', 'yearly archives date format' ) );
            } elseif ( is_month() ) {
                $data['cat_name'] = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
            } elseif ( is_day() ) {
                $data['cat_name'] = get_the_date();
            }


            $list_keywords = 0;
            if(preg_match('#%list_keywords%#',implode('',$meta))){
                $list_keywords = 1;
            }

            if($list_keywords && $wp_query->found_posts){

                $post_ids = array();
                foreach ( $wp_query->posts as $p ) {
                    $post_ids[] = $p->ID;
                }
                $post_ids = implode( ',', $post_ids );
                $sql = "SELECT tt.term_id,tt.term_taxonomy_id,count(1) num 
                        FROM $wpdb->term_relationships r , $wpdb->term_taxonomy tt,$wpdb->terms t 
                        WHERE r.object_id IN($post_ids) AND r.term_taxonomy_id=tt.term_taxonomy_id 
                        AND tt.taxonomy<>'$query_taxonomy' GROUP BY tt.term_taxonomy_id ORDER BY num DESC LIMIT 3";

                $sql = "SELECT t.name FROM $wpdb->terms t ,($sql) tmp WHERE  tmp.term_id=t.term_id ";
                //print_r([$query_taxonomy]);
                //echo $sql;
                $col = $wpdb->get_col( $sql );
                if ( $col ) {
                    $data['list_keywords'] = implode(',',$col);
                }

            }



            if ( isset( $meta[0] ) && $meta[0] ) {
                $title = self::parse_tpl($meta[0],$data,'t');
            }
            if ( isset( $meta[1] ) && $meta[1] ) {
                //$kw = $meta[1];
                $kw = self::parse_tpl($meta[1],$data,'k');
            }
            if ( isset( $meta[2] ) && $meta[2] ) {
                //$desc = $meta[2];
                $desc = self::parse_tpl($meta[2],$data,'d');
            }


		} else if ( is_single() || is_page() || is_singular() ) {

            $slug = 'post';
            if(is_page()){
                $slug = 'page';
            }
            $meta = self::cnf('tdk.'.$slug,array('','',''));
            if(!is_array($meta)){
                $meta = ['','',''];
            }
            $data = [];


            $data['page_title'] = '';
            $data['post_title'] = '';
            $data['cat_name'] = '';
            $data['author_name'] = '';
            $data['parent_cat'] = '';
            $data['post_tag'] = '';
            $data['description'] = '';
            $title = $kw = $desc = '';

			do {

                if(isset($meta[0]) && $meta[0]){
                    $title = trim($meta[0]);
                }
                if(isset($meta[1]) && $meta[1]){
                    $kw = trim($meta[1]);
                }
                if(isset($meta[2]) && $meta[2]){
                    $desc = trim($meta[2]);
                }
				$post = get_queried_object();
				if ( ! ( $post instanceof WP_Post ) ) {
					break;
				}



                $data['page_title'] = $post->post_title;
                $data['post_title'] = $post->post_title;

				$seo_meta = get_post_meta( $post->ID, 'wb_sst_seo', true );
				if ( ! $seo_meta || ! is_array( $seo_meta ) ) {
					$seo_meta = ['','',''];
				}

				if ( $seo_meta[0] ) {
					$title = trim($seo_meta[0]).'%separator%%site_title%' ;
				}
				if(!$title){
				    $title = '%post_title%%separator%%site_title%';
                }


				if ( $seo_meta[1] ) {
					$kw = trim($seo_meta[1]);
				}

				if(!$kw){
				    $kw = '%post_tag%';
                }


				if ( $seo_meta[2] ) {
					$desc = $seo_meta[2];
				}

				if(!$desc){
                    $desc = '%description%';//self::excerpt( $post );
				}

				$search = implode('',[$title,$kw,$desc]);


				if(preg_match('#%description%#',$search)){
                    $data['description'] = self::excerpt( $post );
                }

                if(preg_match('#%author_name%#',$search)){
                    if($post->post_author){
                        $author = get_user_by('ID',$post->post_author);
                        $data['author_name'] = $author?$author->display_name:'';
                    }
                }

                if(preg_match('#%cat_name%#',$search)){
                    $cats = get_the_category($post->ID);
                    //print_r($cats);


                    $cat_names = [];
                    $parent_cat = [];
                    if($cats)foreach($cats as $cat){
                        $cat_names[] = $cat->name;

                        if(preg_match('#%parent_cat%#',$search)){

                            $parents = get_ancestors( $cat->term_id,$cat->taxonomy, 'taxonomy' );
                            foreach ( array_reverse( $parents ) as $term_id ) {
                                $parent = get_term($term_id, $cat->taxonomy);
                                $parent_cat[] = $parent->name;
                            }

                        }

                    }

                    if($parent_cat){
                        $parent_cat = array_unique($parent_cat);
                        $data['parent_cat'] = implode('/',$parent_cat);
                    }

                    if($cat_names){
                        $data['cat_name'] = implode('、',$cat_names);
                    }
                }

                if(preg_match('#%post_tag%#',$search)){

                    $posttags = array();
                    if ( $post->post_type == 'post' ) {
                        $posttags = get_the_terms( $post->ID, 'post_tag' );
                    } else if ( $post->post_type == 'page' ) {
                        $posttags = array();
                    } else {
                        $taxonomies = get_object_taxonomies( $post->post_type, 'object' );
                        foreach ( $taxonomies as $object ) {
                            if ( ! $object->hierarchical && $object->public && preg_match( '#tag#', $object->meta_box_cb ) ) {
                                $posttags = get_the_terms( $post->ID, $object->name );
                                break;
                            }
                        }
                    }

                    //kw
                    if ( $posttags ) {
                        $tags = array();
                        foreach ( $posttags as $tag ) {
                            $tags[] = $tag->name;
                        }
                        $data['post_tag'] = implode( ',', $tags );
                        //$kw    = $stags;
                    }

                    /*$post_tags = get_the_tags($post->ID);
                    $tags = [];
                    if($post_tags)foreach ($post_tags as $tag){
                        $tags[] = $tag->name;
                    }
                    $data['post_tag'] = implode('、',$tags);*/
                }



			} while ( 0 );

			if($title){
			    $title = self::parse_tpl($title,$data,'t');
            }
            if($kw){
                $kw = self::parse_tpl($kw,$data,'k');
            }
            if($desc){
                $desc = self::parse_tpl($desc,$data,'d');
            }

		}


		return apply_filters('wb_seo_info',array( 'title' => $title, 'kw' => $kw, 'desc' => $desc ));
	}

	//seo title, keywords, description
	public static function seoTitle() {
		$title = wp_get_document_title();
		$kw    = $desc = '';
		$tpls  = array( '<title>%s</title>', '%s', '%s' );
		//$seo = $this->opt('seo');
		if ( is_home() ) {
			$meta = self::cnf( 'tdk.index', array( '', '', '' ) );
			if ( isset( $meta[0] ) && $meta[0] ) {
				$title = self::formatTitle( $meta[0] );
			}
			if ( isset( $meta[1] ) && $meta[1] ) {
				$tpls[1] = '<meta name="keywords" content="%s" />';
				$kw      = $meta[1];
			}
			if ( isset( $meta[2] ) && $meta[2] ) {
				$tpls[2] = '<meta name="description" content="%s" />';
				$desc    = $meta[2];
			}

		} else if ( is_author() ) {

			/* 标题: 「{author_name}」作者主页 - {sitename}
              关键词: 读取该作者所有文章Top5热门关键词，以英文逗号分隔
              描述:  「{author_name}」作者主页， 「{author_name}」主要负责{该作者所有文章Top5热门关键词（以顿号分割）}等内容发布。
			  注：{author_name}指作者昵称
            */

			global $authordata, $wpdb;

			$sep   = apply_filters( 'document_title_separator', '-' );
			$title = implode( $sep, array( '「' . get_the_author() . '」作者主页', get_bloginfo( 'name', 'display' ) ) );
			//$title = self::formatTitle($title);

			if ( is_object( $authordata ) ) {

				$top_words = get_user_meta( $authordata->ID, 'seo_top_keywords', true );
				$time      = current_time( 'timestamp' );
				if ( ! $top_words || $top_words['time'] < $time ) {

					$sql = "SELECT c.`term_taxonomy_id`,c.term_id,COUNT(1) num FROM $wpdb->posts a,$wpdb->term_relationships r,$wpdb->term_taxonomy c ";
					$sql .= " WHERE a.post_author=%d AND a.post_status='publish' AND a.post_type='post' AND a.ID=r.object_id AND r.term_taxonomy_id=c.term_taxonomy_id AND c.taxonomy='post_tag'";
					$sql .= " GROUP BY c.`term_taxonomy_id` ORDER by num DESC LIMIT 5";

					$sql = "SELECT t.name from $wpdb->terms t,($sql) tt WHERE t.term_id=tt.term_id";

					$sql = $wpdb->prepare( $sql, $authordata->ID );

					$col = $wpdb->get_col( $sql );

					$top_words = array( 'time' => $time + WEEK_IN_SECONDS, 'keywords' => $col );
					update_user_meta( $authordata->ID, 'seo_top_keywords', $top_words );
				}

				if ( $top_words['keywords'] ) {
					$tpls[1] = '<meta name="keywords" content="%s" />';
					$kw      = implode( ',', $top_words['keywords'] );
					$tpls[2] = '<meta name="description" content="%s" />';
					$desc    = '「' . get_the_author() . '」作者主页，主要负责' . implode( '、', $top_words['keywords'] ) . '等内容发布。';
				}

			}


		} else if ( is_search() ) {

			//$q = get_queried_object();
			//print_r($q);
			global $wp_query, $wpdb;
			/*
            标题: 与「{search_keyword}」匹配搜索结果 - {sitename}
            关键词: {search_keyword}, {search_keyword}相关, {search_keyword}内容, 搜索结果所有文章Top5热门关键词
            描述: 当前页面展示所有与「{search_keyword}」相关的匹配结果，包括搜索结果文章Top5关键词（以顿号分割）等内容。
            注：{search_keyword}指访客搜索关键词
            */
			$sep   = apply_filters( 'document_title_separator', '-' );
			$q_kw  = get_search_query( false );
			$title = implode( $sep, array( '与「' . $q_kw . '」匹配的搜索结果', get_bloginfo( 'name', 'display' ) ) );
			//$title = self::formatTitle($title);

			$kws     = array( $q_kw, $q_kw . '相关', $q_kw . '内容' );//array_merge(,$top_words['keywords']);
			$tpls[1] = '<meta name="keywords" content="%s" />';
			$kw      = implode( ',', $kws );
			$tpls[2] = '<meta name="description" content="%s" />';
			$desc    = '当前页面展示所有与「' . $q_kw . '」搜索词相匹配的结果';//.implode('、',$top_words['keywords']);

			if ( $wp_query->found_posts ) {
				$post_ids = array();
				foreach ( $wp_query->posts as $p ) {
					$post_ids[] = $p->ID;
				}
				//print_r($wp_query);

				$post_ids = implode( ',', $post_ids );

				$sql = "SELECT tt.term_id,tt.term_taxonomy_id,count(1) num FROM $wpdb->term_relationships r , $wpdb->term_taxonomy tt,$wpdb->terms t where r.object_id IN($post_ids) AND r.term_taxonomy_id=tt.term_taxonomy_id AND tt.taxonomy<>'category' group by tt.term_taxonomy_id order by num DESC LIMIT 5";

				$sql = "SELECT t.name FROM $wpdb->terms t ,($sql) tmp where  tmp.term_id=t.term_id ";


				//echo $sql;
				$col = $wpdb->get_col( $sql );
				if ( $col ) {

					$kw   .= ',' . implode( ',', $col );
					$desc .= ',包括' . implode( '、', $col ) . '等内容。';
				}

			}


		} else if ( is_tag() ) {

			$tag = get_queried_object();

			global $wpdb;
			//print_r($tag);
			/* 标题: 「{tag}」相关文章列表 - 站点名称
            关键词: {tag}, {tag}相关, {tag}内容及标签结果文章Top5关键词
            描述: 关于「{tag}」相关内容全站索引列表，包括标签列表页所有结果Top5关键词（以顿号分割）。
            注：{tag}指文章编辑时输入的标签词语*/


			$sep   = apply_filters( 'document_title_separator', '-' );
			$title = implode( $sep, array( '「' . $tag->name . '」相关文章列表', get_bloginfo( 'name', 'display' ) ) );
			//$title = self::formatTitle($title);

			$top_words = get_term_meta( $tag->term_id, 'seo_top_keywords', true );
			$time      = current_time( 'timestamp' );
			if ( ! $top_words || $top_words['time'] < $time ) {


				//tag 下的所有文章
				$sql = "SELECT p.ID  FROM $wpdb->term_relationships r ,$wpdb->posts p WHERE r.term_taxonomy_id = %d and  r.object_id=p.ID AND p.post_status='publish'";

				//所有文章下的tag，取数量前五
				$sql = "SELECT tt.term_taxonomy_id,tt.term_id,COUNT(1) FROM $wpdb->term_taxonomy tt ,$wpdb->term_relationships rr ,$wpdb->posts pp WHERE tt.term_taxonomy_id=rr.term_taxonomy_id  AND tt.taxonomy<>'category' AND rr.object_id=pp.ID AND pp.ID IN($sql)";
				$sql .= " GROUP BY tt.term_taxonomy_id ORDER BY tt.count DESC LIMIT 5";


				$sql = "SELECT t.name FROM $wpdb->terms t,($sql) tmp WHERE t.term_id=tmp.term_id";

				$sql = $wpdb->prepare( $sql, $tag->term_taxonomy_id );

				//echo $sql;exit();
				$col = $wpdb->get_col( $sql );

				$top_words = array( 'time' => $time + WEEK_IN_SECONDS, 'keywords' => $col );
				update_term_meta( $tag->term_id, 'seo_top_keywords', $top_words );
			}

			if ( $top_words['keywords'] ) {
				$kws     = array_merge( array(
					$tag->name,
					$tag->name . '相关',
					$tag->name . '内容'
				), $top_words['keywords'] );
				$tpls[1] = '<meta name="keywords" content="%s" />';
				$kw      = implode( ',', $kws );
				$tpls[2] = '<meta name="description" content="%s" />';
				$desc    = '关于「' . $tag->name . '」相关内容全站索引列表，包括' . implode( '、', $top_words['keywords'] ) . '。';
			}

		} else if ( is_category() ) {
			$term = get_queried_object();
			$cid  = $term->term_id;
			$meta = self::cnf( 'tdk.'.$cid, array( '', '', '' ) );
			if ( isset( $meta[0] ) && $meta[0] ) {
				$sep   = apply_filters( 'document_title_separator', '-' );
				$title = implode( $sep, array( $meta[0], get_bloginfo( 'name', 'display' ) ) );
				$title = self::formatTitle( $title );
			}
			if ( isset( $meta[1] ) && $meta[1] ) {
				$tpls[1] = '<meta name="keywords" content="%s" />';
				$kw      = $meta[1];
			}
			if ( isset( $meta[2] ) && $meta[2] ) {
				$tpls[2] = '<meta name="description" content="%s" />';
				$desc    = $meta[2];
			}
		} else if ( is_single() || is_page() ) {


			//kw
			$posttags = get_the_tags();

			if ( $posttags ) {
				$tags = array();
				foreach ( $posttags as $tag ) {
					$tags[] = $tag->name;
				}
				$stags   = implode( ',', $tags );
				$kw      = $stags;
				$tpls[1] = '<meta name="keywords" content="%s" />';
			}
			//desc
			$excerpt = self::excerpt();

			if ( $excerpt ) {
				$desc    = $excerpt;
				$tpls[2] = '<meta name="description" content="%s" />';
			}

		}
		echo sprintf( implode( "\n", $tpls ), esc_attr($title), esc_attr($kw), esc_attr($desc) );
	}

	//格式化标题
	public static function formatTitle( $title ) {
		$title = wptexturize( $title );
		$title = convert_chars( $title );
		$title = esc_html( $title );
		$title = capital_P_dangit( $title );

		return $title;
	}

	//文章摘要
	public static function excerpt( $post = null ) {
		if ( ! $post ) {
			$post = get_post();
		}
		if ( empty( $post ) ) {
			return '';
		}

		$excerpt = $post->post_excerpt ? $post->post_excerpt : self::trimContent( $post->post_content );
		if ( ! $excerpt ) {
			return $excerpt;
		}

		return apply_filters( 'get_the_excerpt', $excerpt, $post );
	}

	//格式化文章内容
	public static function trimContent( $text ) {
		$text           = strip_shortcodes( $text );
		$excerpt_length = 100;//apply_filters('excerpt_length', 120);
		$text           = wp_trim_words( $text, $excerpt_length, '' );

		return $text;
	}
}