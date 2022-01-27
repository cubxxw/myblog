<?php
/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com/
 */

class Smart_SEO_Tool_Rewrite {
	public function __construct(){

	}

	public static function cnf($key,$default=null){
	    return Smart_SEO_Tool_Admin::cnf($key,$default);
    }

	public static function init(){

        if(self::cnf('url_seo.active')){
            if(self::cnf('url_seo.reset_tag')) {
                self::rewrite_tag();
            }
            if(self::cnf('url_seo.hide_category')){
                self::no_category();
            }
            if(self::cnf('url_seo.set_gopage')) {
                self::rewrite_go();
            }

            add_filter('the_content',array(__CLASS__,'the_content'),40);
        }
        add_action('parse_query',array(__CLASS__,'parse_query'));
        add_action('wb_sst_option_update',function($new,$old){
            if($new['url_seo']['active'] != $old['url_seo']['active']){
                if($new['url_seo']['active']){//0->1
                    self::update_rewrite($new,$old,1);
                }else{//1->0
                    self::update_rewrite($new,$old,0);
                }

            }else if($new['url_seo']['active']){//1->1
                self::update_rewrite($new,$old,2);
            }

        },11,2);
    }

    public static function parse_query($obj)
    {
        $seo = self::cnf('url_seo');
        if(is_tag() && $seo && isset($seo['active']) && $seo['active'] && isset($seo['reset_tag']) && $seo['reset_tag']){
            if(isset($obj->query['tag'])){
                $q = get_queried_object();
                if($q instanceof WP_Term){
                    wp_redirect(get_term_link($q,$q->taxonomy),301);
                    exit();
                }
            }
        }

    }

    public static function update_rewrite($new,$old,$state = null)
    {
        global $wp_rewrite;
        if(!$wp_rewrite->using_permalinks()){
            return;
        }
        $new = $new['url_seo'];
        $old = $old['url_seo'];
        if(0 === $state){//close all, remove all rewrite

            self::remove_category_permastruct();
            remove_filter('category_rewrite_rules', array(__CLASS__,'category_rewrite_rules'));

            remove_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
            remove_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));

            $wp_rewrite->flush_rules();
            return;
        }

        //1 open status new , 2 open state old
        if($new['reset_tag'] != $old['reset_tag']){//rewrite tag
            if($new['reset_tag']){//0->1
                //add_action('generate_rewrite_rules',array(__CLASS__,'tag_rewrite_rules'));
                add_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
                add_filter('post_tag_rewrite_rules',array(__CLASS__,'post_tag_rewrite_rules'));
            }else{//1-0
                //remove_action('generate_rewrite_rules',array(__CLASS__,'tag_rewrite_rules'));
                remove_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
                remove_filter('post_tag_rewrite_rules',array(__CLASS__,'post_tag_rewrite_rules'));
            }
        }else if($new['reset_tag'] && 1 === $state){
            //add_action('generate_rewrite_rules',array(__CLASS__,'tag_rewrite_rules'));
            add_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
            add_filter('post_tag_rewrite_rules',array(__CLASS__,'post_tag_rewrite_rules'));
        }
        if($new['hide_category'] != $old['hide_category']){
            if($new['hide_category']){//0->1
                add_filter('category_rewrite_rules', array(__CLASS__,'category_rewrite_rules'));
                self::category_permastruct();
            }else{//1->0
                remove_filter('category_rewrite_rules', array(__CLASS__,'category_rewrite_rules'));
                self::remove_category_permastruct();
            }
        }else if($new['hide_category'] && 1 === $state){
            add_filter('category_rewrite_rules', array(__CLASS__,'category_rewrite_rules'));
            self::category_permastruct();
        }
        if($new['set_gopage']!=$old['set_gopage']){
            if($new['set_gopage']){//0->1
                //add_action('generate_rewrite_rules',array(__CLASS__,'go_rewrite_rules'));
                add_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));
            }else{//1->0
                //remove_action('generate_rewrite_rules',array(__CLASS__,'go_rewrite_rules'));
                remove_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));
            }
        }else if($new['set_gopage'] && 1 === $state){
            //add_action('generate_rewrite_rules',array(__CLASS__,'go_rewrite_rules'));
            add_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));
        }
        $wp_rewrite->flush_rules();
    }

    public static function flush_rewrite(){
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public static function remove_rewrite(){

        //remove_action('generate_rewrite_rules',array(__CLASS__,'go_rewrite_rules'));
        remove_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));

        //remove_action('generate_rewrite_rules',array(__CLASS__,'tag_rewrite_rules'));
        remove_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
        remove_filter('post_tag_rewrite_rules',array(__CLASS__,'post_tag_rewrite_rules'));

        self::remove_category_permastruct();
        remove_filter('category_rewrite_rules',array(__CLASS__,'category_rewrite_rules'));
        //wp_clear_scheduled_hook('sm_ping_daily');
    }

    public static function rewrite_go(){

        //add_action('generate_rewrite_rules',array(__CLASS__,'go_rewrite_rules'));
        add_filter('rewrite_rules_array',array(__CLASS__,'go_rewrite_rules_array'));
        add_filter('query_vars',array(__CLASS__,'go_query_vars'));
        add_action('parse_request',array(__CLASS__,'go_302'));
    }

    public static function go_rewrite_rules_array($rules){
        $new_rules = array(
            'go/?$' => 'index.php?go=1',
        );
        return $new_rules + $rules;
    }
    public static function go_rewrite_rules($wp_rewrite){
        $new_rules = array(
            'go/?$' => 'index.php?go=1',
        );
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    public static function go_query_vars($public_query_vars){
        $public_query_vars[] = 'go';
	    return $public_query_vars;
    }

    public static function go_302($wp) {
        $query_vars = $wp->query_vars;
        $redirect = isset($_GET['_']) && $_GET['_'] ? trim(sanitize_text_field($_GET['_'])):null;
        if( isset( $query_vars['go'] ) && $query_vars['go'] && $redirect ) {
            $nonce = substr($redirect,0,10);
            $url = base64_decode(substr($redirect,10));
            //header('content-type:text/html;charset="utf-8"');
            if($url && $nonce && self::redirect_nonce($url,$nonce)) {
                header("Location: $url");
                exit(0);
            }
            $wp->query_vars['pagename'] = 'go-404';
        }
    }

    public static function redirect_nonce($url,$nonce = null){
        $key = home_url();
        if(defined('NONCE_KEY')){
            $key = NONCE_KEY;
        }else if(defined('AUTH_KEY')){
            $key = AUTH_KEY;
        }

        $source = substr(md5($key.$url),12,10);

	    if(null === $nonce){
	        return $source;
        }

        return $source == $nonce;
    }

    public static function the_content($content){
	    if(!is_single()){
	        return $content;
        }
	    $rewrite_go = self::cnf('url_seo.set_gopage');
	    $rewrite_rel = self::cnf('url_seo.set_nofollow');
	    $rewrite_target = self::cnf('url_seo.blank');

	    if(!($rewrite_go || $rewrite_rel || $rewrite_target)){
	        return $content;
        }

        if(!preg_match_all('#<a.+?href=[^>]+>#',$content,$match)){
            return $content;
        }



        $exclude = self::cnf('url_seo.exclude',array());


        foreach ($match[0] as $k=>$v){

            $search_html = $v;

            if(!preg_match('#href=("|\')(.+?)\1#',$v,$sm)){
                continue;
            }

            if(!$sm[2]){
                continue;
            }

            $url = trim($sm[2]);

            $se_url = $url;
            if(!preg_match('#^https?://#',$url)){
                continue;
            }
            if(strpos($url,home_url())===0){
                continue;
            }
            if($exclude){
                $find_it = false;
                foreach($exclude as $domain){
                    if(preg_match('#://[^/]*'.preg_quote($domain).'/?#',$url)){
                        $find_it = true;
                        break;
                    }
                }

                if($find_it){
                    continue;
                }
            }


            $replace_html = $search_html;

            if($rewrite_go){
                $nonce = self::redirect_nonce($url);
                $url = home_url('/go?_=').urlencode($nonce.base64_encode($url));
                $replace_html = str_replace($se_url,$url,$search_html);
            }

            if($rewrite_rel){
                if(preg_match('#rel="([^"]+)"#is',$replace_html,$m) || preg_match("#rel='[^']+'#is",$replace_html,$m) ){
                    $a = explode(' ',$m[1]);

                    if(!in_array('noopener',$a)){
                        array_push($a,'noopener');
                    }
                    if(!in_array('noreferrer',$a)){
                        array_push($a,'noreferrer');
                    }
                    if(!in_array('nofollow',$a)){
                        array_push($a,'nofollow');
                    }
                    $replace_html = str_replace($m[1],implode(' ',$a),$replace_html);
                    /*if(!preg_match('#nofollow#i',$m[1])){
                        $replace_html = str_replace($m[1],$m[1].' nofollow',$replace_html);
                    }*/

                }else if(!preg_match('#rel=.*?nofollow.*?#is',$replace_html)){

                    $replace_html = substr($replace_html,0,-1).' rel="noopener noreferrer nofollow">';
                }
            }
            if($rewrite_target){
                if(!preg_match('#target=#i',$replace_html)){
                    $replace_html = substr($replace_html,0,-1).' target="_blank">';
                }

            }

            $content = str_replace($search_html,$replace_html,$content);


        }

        return $content;
    }

    public static function rewrite_tag(){

        //add_action('generate_rewrite_rules',array(__CLASS__,'tag_rewrite_rules'));
        add_filter('rewrite_rules_array',array(__CLASS__,'tag_rewrite_rules_array'));
        add_filter('post_tag_rewrite_rules',array(__CLASS__,'post_tag_rewrite_rules'));
        add_filter('term_link',array(__CLASS__,'tag_term_link'),10,3);

        add_action('query_vars', array(__CLASS__,'tag_query_vars'));
    }

    public static function post_tag_rewrite_rules($rules){

	    return $rules;
	    //return array();
    }

    public static function tag_rewrite_rules_array($rules){
        $new_rules = array(
            'tag/(\d+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?tag_id=$matches[1]&feed=$matches[2]',
            'tag/(\d+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?tag_id=$matches[1]&feed=$matches[2]',
            'tag/(\d+)/embed/?$' => 'index.php?tag_id=$matches[1]&embed=true',
            'tag/(\d+)/page/(\d+)/?$' => 'index.php?tag_id=$matches[1]&paged=$matches[2]',
            'tag/(\d+)/?$' => 'index.php?tag_id=$matches[1]',
        );
        return $new_rules + $rules;
    }

    public static function tag_rewrite_rules($wp_rewrite){

        $new_rules = array(
            'tag/(\d+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?tag_id=$matches[1]&feed=$matches[2]',
            'tag/(\d+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?tag_id=$matches[1]&feed=$matches[2]',
            'tag/(\d+)/embed/?$' => 'index.php?tag_id=$matches[1]&embed=true',
            'tag/(\d+)/page/(\d+)/?$' => 'index.php?tag_id=$matches[1]&paged=$matches[2]',
            'tag/(\d+)/?$' => 'index.php?tag_id=$matches[1]',
        );
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

    }

    public static function tag_term_link($link,$term,$taxonomy){
        if($taxonomy=='post_tag'){
            return home_url('/tag/'.$term->term_id);
        }

        return $link;
    }

    public static function tag_query_vars($public_query_vars){

        $public_query_vars[] = 'tag_id';
        return $public_query_vars;
    }

    public static function category_rewrite_name()
    {
        $base = trim(get_option('category_base'));
        if(!$base){
            $base = 'category';
        }
        return $base.'_rewrite_rules';
    }

    public static function no_category(){
	    //print_r(__CLASS__);exit();
        add_action('created_category',  array(__CLASS__,'flush_rewrite'));
        add_action('delete_category',   array(__CLASS__,'flush_rewrite'));
        add_action('edited_category',   array(__CLASS__,'flush_rewrite'));

        add_action('init',array(__CLASS__,'category_permastruct'));


        add_filter('category_rewrite_rules', array(__CLASS__,'category_rewrite_rules'));


        add_filter('query_vars',array(__CLASS__,'category_query_vars'));
        //add_filter('request',function($query_vars){return $query_vars;});
        add_action('parse_request',array(__CLASS__,'category_301'));

    }

    public static function category_rewrite_rules($category_rewrite) {
        global $wp_rewrite;
        $category_rewrite=array();

        $categories = get_categories( array( 'hide_empty' => false ) );

        foreach( $categories as $category ) {
            $category_slug = $category->slug;

            if ( $category->parent == $category->cat_ID ) {
                $category->parent = 0;
            }
            if ( $category->parent != 0 ) {
                $category_slug = get_category_parents( $category->parent, false, '/', true ) . $category_slug;
            }

            $category_rewrite['('.$category_slug.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
            $category_rewrite["({$category_slug})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
            $category_rewrite['('.$category_slug.')/?$'] = 'index.php?category_name=$matches[1]';
        }

        $slug = get_option( 'category_base' );
        if(empty($slug)){
            $slug = 'category';
        }

        $category_rewrite[$slug.'/(.*)$'] = 'index.php?category_301=$matches[1]';

        return $category_rewrite;
    }

    public static function remove_category_permastruct(){
        global $wp_rewrite;
        $slug = get_option( 'category_base' );
        if(empty($slug)){
            $slug = 'category';
        }
        $wp_rewrite->extra_permastructs['category']['struct'] = $slug.'/%category%';
    }

    public static function category_permastruct(){
        global $wp_rewrite;
        /*if(!$wp_rewrite->using_permalinks()){
            return;
        }*/
        $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
    }

    public static function category_query_vars($public_query_vars) {
        $public_query_vars[] = 'category_301';
        return $public_query_vars;
    }

    public static function category_301($wp) {
        $query_vars = $wp->query_vars;
        if( isset( $query_vars['category_301'] ) && $query_vars['category_301']  ) {
            $redirect = home_url('/').user_trailingslashit($query_vars['category_301'],'category');
            header( "Location: $redirect" ,true,301);
            exit();
        }
    }



}