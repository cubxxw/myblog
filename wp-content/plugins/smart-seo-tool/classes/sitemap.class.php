<?php
class Smart_SEO_Tool_Sitemap {


    public static $pagesize = 10000;

    public static $last_post_transient_id = 'wb_sst_lastest_post_id';

	public function __construct(){



	}

	public static function log($msg)
    {
        return;
        error_log(current_time('mysql').$msg."\n",3,__DIR__.'/ping.log');

    }

    public static function cnf($key,$default=null){
        return Smart_SEO_Tool_Admin::cnf($key,$default);
    }


    public static function post_types()
    {
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        unset( $post_types['attachment'] );
        unset( $post_types['post_format'] );

        $post_types = array_filter( $post_types, 'is_post_type_viewable' );

        return $post_types;
    }

    public static function is_taxonomy_viewable( $taxonomy ) {
        if ( is_scalar( $taxonomy ) ) {
            $taxonomy = get_taxonomy( $taxonomy );
            if ( ! $taxonomy ) {
                return false;
            }
        }

        return $taxonomy->publicly_queryable;
    }

    public static function taxonomies()
    {

        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

        if(!$taxonomies){
            return $taxonomies;
        }

        $taxonomies = array_filter( $taxonomies, array(__CLASS__,'is_taxonomy_viewable') );

        return $taxonomies;

    }
	public static function init(){


        add_action('wb_sst_option_update',function($new,$old){

            global $wp_rewrite;
            if(!$wp_rewrite->using_permalinks()){
                return;
            }
            if($new['sitemap_seo']['active'] != $old['sitemap_seo']['active']) {

                if($new['sitemap_seo']['active']){
                    //add_action('generate_rewrite_rules', array(__CLASS__, 'rewrite_rules'));
                    add_filter('rewrite_rules_array',array(__CLASS__,'rewrite_rules_array'));
                }else{
                    //remove_action('generate_rewrite_rules',array(__CLASS__,'rewrite_rules'));
                    remove_filter('rewrite_rules_array',array(__CLASS__,'rewrite_rules_array'));
                }
                $wp_rewrite->flush_rules();
            }

        },10,2);

        if(self::cnf('sitemap_seo.active')){

            //add_action('generate_rewrite_rules',array(__CLASS__,'rewrite_rules'));
            add_filter('rewrite_rules_array',array(__CLASS__,'rewrite_rules_array'));
            add_filter('query_vars',array(__CLASS__,'query_vars'));
            add_action('parse_request',array(__CLASS__,'parse_request'));



            /*if(self::cnf('sitemap_seo.push_to.robots')){

            }*/




            if(self::cnf('sitemap_seo.push_to.google') || self::cnf('sitemap_seo.push_to.bing')) {
                //ping
                add_action('wb_sst_ping', array(__CLASS__, 'send_ping'), 10, 1);

                //daily ping
                add_action('wb_sst_ping_daily', array(__CLASS__, 'ping_daily'), 10, 1);

                add_action('transition_post_status', array(__CLASS__, 'transition_post_status'), 9999, 3);

                if (!wp_get_schedule('wb_sst_ping_daily')) {
                    wp_schedule_event(time() + (60 * 60), 'daily', 'wb_sst_ping_daily');
                }
            }else{
                wp_clear_scheduled_hook('wb_sst_ping_daily');
            }


            add_filter('robots_txt',array(__CLASS__,'sitemap_robots'),20,2);
        }

        /*if(self::cnf('robots_seo.active')){
            add_filter('robots_txt',array(__CLASS__,'robots'),11,2);
        }else */
        /*if(self::cnf('sitemap_seo.active')){

        }*/

    }

    public static function remove_rewrite(){
	    //remove_action('generate_rewrite_rules',array(__CLASS__,'rewrite_rules'));
	    remove_filter('rewrite_rules_array',array(__CLASS__,'rewrite_rules_array'));

        wp_clear_scheduled_hook('wb_sst_ping_daily');

    }


    public static function transition_post_status($new_status, $old_status, $post ) {
        if($new_status == 'publish') {
            set_transient(self::$last_post_transient_id, $post->ID, 120);
            wp_schedule_single_event(time() + 5, 'wb_sst_ping');
        }
    }


    public static function post_sitemap($post_id){
	    global $wpdb;

	    $post = $wpdb->get_row($wpdb->prepare("select YEAR(post_date) ym from $wpdb->posts where  ID=%d AND  post_type='post' AND post_status='publish'",$post_id));

	    if($post){
	        return home_url('/sitemap-post-'.$post->ym.'.xml');
        }
        return '';
    }

    public static function ping_daily(){

        $lastest_post_time = strtotime(get_lastpostdate('blog'));
        $last_daily_ping_time = get_option('wb_sst_daily_ping_time',0);

        if($lastest_post_time>$last_daily_ping_time) {
            self::send_ping();
            update_option('wb_sst_daily_ping_time',$lastest_post_time,false);
        }
    }

    public static function send_ping(){

	    self::ping(home_url('/sitemap.xml'));

        $post_id = get_transient(self::$last_post_transient_id);

        self::log(' send_ping');

        if($post_id){
            $post_sitemap = self::post_sitemap($post_id);
            self::log('post_sitemap '.$post_sitemap);

            if($post_sitemap){
                self::ping($post_sitemap);
            }
            delete_transient(self::$last_post_transient_id);
        }
    }
    public static function ping($sitemap_url){



	    $cnf = self::cnf('sitemap_seo.push_to');
        $ping_serv = array();

        if($cnf['google']){
            //$ping_serv['google'] = 'https://www.google.com/webmasters/sitemaps/ping?sitemap=';
            $ping_serv['google'] = 'https://bsl.api.wbolt.com/ping.php?sitemap=';
        }

        if($cnf['bing']) {
            $ping_serv['bing'] = 'https://www.bing.com/webmaster/ping.aspx?siteMap=';
        }

        self::log('ping serv '.json_encode($ping_serv));

        foreach ($ping_serv AS $name=>$ping_url) {
            $url = $ping_url.urlencode($sitemap_url);
            $response = wp_remote_get($url,array('timeout'=>5,'sslverify'=>false));
            /*if(is_wp_error($response)){
                self::log($response->get_error_message());
            }else{
                self::log(wp_remote_retrieve_response_code($response));
            }*/

            /*if(is_wp_error($response) && $name=='google'){
                $proxy_url = 'https://proxy.wbolt.com/wp-admin/admin-ajax.php?action=down_img&url='.urlencode($url);
                $response = wp_remote_get($proxy_url,array('timeout'=>5,'sslverify' => false));
            }

            //print_r($response);
            if(!is_wp_error($response) && $response['response']['code'] == 200){
                $response['body'];
            }*/
        }
    }



    public static function robots_txt()
    {

        $output = "User-agent: *\n";
        $site_url = parse_url(site_url());
        $path = (!empty($site_url['path'])) ? $site_url['path'] : '';
        $output .= "Disallow: $path/wp-admin/\n";
        $output .= "Disallow: $path/wp-include/\n";
		$output .= "Disallow: $path/wp-login.php?redirect_to=*\n";
        $output .= "Disallow: $path/go?_=*\n";
        $output .= "Allow: $path/wp-admin/admin-ajax.php\n";
        $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";

        return $output;
    }

    public static function sitemap_robots($output, $public)
    {
        if ( $public ) {
            $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
        }
        return $output;
    }

    public static function robots($output, $public){

        if ( '0' == $public ) {

        }else{
            $txt = self::robots_txt();

            $a = explode("\n",$txt);

            $output = self::cnf('robots_seo.content');
            foreach($a as $s){
                $output = str_replace("$s",'',$output);
            }
            $output = trim($output);
            $output = $txt.$output;


            if(!self::cnf('sitemap_seo.active')){
                $s = "Sitemap: ".home_url('/sitemap.xml');
                $output = str_replace($s,'',$output);
            }
            if(!self::cnf('url_seo.active') || !self::cnf('url_seo.set_gopage')){
                $site_url = parse_url( site_url() );
                $path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';
                $s = "#Disallow: ".preg_quote($path)."/go\?.*#";
                $output = preg_replace($s,'',$output);
            }
            $output = trim($output);

            $output = str_replace("\r\n","\n",$output);

            $a = explode("\n",$output);
            $output = implode("\n",array_filter($a));
        }

        return $output;
    }

    public static function rewrite_rules_array($rules)
    {
        $new_rules = array(
            'sitemap(-([a-zA-Z0-9_-]+))?\.xml$' => 'index.php?wb_sitemap=$matches[2]',
        );
        return $new_rules + $rules;
    }

    public static function rewrite_rules($wp_rewrite){
        $new_rules = array(
            'sitemap(-([a-zA-Z0-9_-]+))?\.xml$' => 'index.php?wb_sitemap=$matches[2]',
        );
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }

    public static function query_vars($public_query_vars){
        $public_query_vars[] = 'wb_sitemap';
        return $public_query_vars;
    }


    public static function parse_request($wp){
	    if(!isset($wp->query_vars['wb_sitemap'])){
	        return;
        }


        date_default_timezone_set(get_option('timezone_string'));
        $param = $wp->query_vars['wb_sitemap'];

	    $xml = '';
	    if(empty($param)){
            $xml = self::index();
        }else{
            $xml = self::content($param);
        }

        header('Content-Type: text/xml; charset=utf-8');
	    echo $xml;
	    exit(0);
    }


    public static function sitemap_index()
    {

        $http = wp_remote_get(home_url('/sitemap.xml'));

        $body = wp_remote_retrieve_body($http);

        if(!$body){
            return array();
        }

        if(!preg_match_all('#<loc>([^<]+)#is',$body,$match)){
            return array();
        }

        return $match[1];

        //<loc>http://wbolt.cn/sitemap-category.xml</loc>


    }
    public static function index(){
        global $wpdb;
	    $xml = self::header('index');


        $blog_update = get_lastpostmodified('blog');

        $cnf = self::cnf('sitemap_seo.content_item');

        //首页
        if($cnf['index']['switch']){

            $xml .= self::add_sitemap("misc",$blog_update);
        }



        $post_types = self::post_types();




        foreach($post_types AS $post_type=>$term) {
            if($post_type=='page')continue;
            if(!isset($cnf[$post_type])){
                continue;
            }
            if(!$cnf[$post_type]['switch']) {
                continue;
            }
            $q = "
                SELECT
                    MAX(p.post_modified) as `last_mod`,
                    YEAR(p.post_date) ym,COUNT(1) num
                FROM
                    {$wpdb->posts} p
                WHERE
                    p.post_password = ''
                    AND p.post_type = '" . esc_sql($post_type) . "'
                    AND p.post_status = 'publish'
                  
                GROUP BY
                    YEAR(p.post_date)
                ORDER BY
                    p.post_date DESC";

            $result_list = $wpdb->get_results($q);

            if($result_list) {
                foreach($result_list as $post) {
                    $max_page = ceil($post->num / self::$pagesize);

                    $xml .= self::add_sitemap($post_type . "-" . $post->ym, $post->last_mod);

                    for($i=1;$i<$max_page;$i++){
                        $xml .= self::add_sitemap($post_type . "-" . $post->ym.'-'.$i, $post->last_mod);
                    }
                }
            }
        }





        $taxonomies = self::taxonomies();

        //文章分类、及post_tag
        foreach($taxonomies as $taxName=>$taxonomy) {
            if(!isset($cnf[$taxName]))continue;
            if(!$cnf[$taxName]['switch']){
                continue;
            }

            /*$taxonomy = get_taxonomy($taxName);
            if(!$taxonomy){
                continue;
            }*/
            $num = wp_count_terms($taxonomy->name, array('hide_empty' => true));
            if($num<1){
                continue;
            }
            //rint_r([$taxonomy->name,$num]);
            $max_page = ceil($num/self::$pagesize);
            for($i=0;$i<$max_page;$i++){

                if(!$i){
                    $xml .= self::add_sitemap($taxonomy->name,$blog_update);
                    continue;
                }

                $xml .= self::add_sitemap($taxonomy->name.'-'.$i,$blog_update);
            }

        }


        if($cnf['page']['switch']){
            $last_mod = $wpdb->get_var("SELECT MAX(post_modified) FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' AND post_password=''");

            if($last_mod){
                $xml .= self::add_sitemap("page" , $last_mod);
            }
        }


        if($cnf['author']['switch']) {
            $xml .= self::add_sitemap("authors", $blog_update);

        }

        if($cnf['archive']['switch']) {
            $xml .= self::add_sitemap("archives", $blog_update);
        }

	    $xml .= self::footer('index');


	    return $xml;

    }



    public static function content($param){
        $xml = self::header('url');


        $query = trim($param);

        $a = explode('-',$param,2);
        $type = '';
        $param = '';
        if(isset($a[0])){
            $type = $a[0];
        }
        if(isset($a[1])){
            $param = $a[1];
        }
        switch ($type){
            case 'misc':
                $xml .= self::content_misc($param);
                break;
            case 'page':
                $xml .= self::content_post('page',null);
                break;
            case 'authors':
                $xml .= self::content_author();
                break;
            case 'archives':
                $xml .= self::content_archives();
                break;
            case 'category':
                $xml .= self::content_taxonomy('category',$param);
                break;
            case 'post_tag':
                $xml .= self::content_taxonomy('post_tag',$param);
                break;
            case 'post':
                $xml .= self::content_post('post',$param);
                break;
            default:
                $match = false;
                $post_types = self::post_types();
                foreach($post_types as $type=>$r){
                    if(in_array($type,array('post','page'))){
                        continue;
                    }
                    if(preg_match('#^'.preg_quote($type).'(-([0-9-]+))?$#',$query,$match)){

                        //print_r($match);
                        $xml .= self::content_post($type,$match[2]);
                        $match = true;
                        break;
                    }
                }

                if($match){
                    break;
                }

                $taxonomies = self::taxonomies();

                foreach($taxonomies as $taxName=>$taxonomy) {
                    if(in_array($taxName,array('category','post_tag'))){
                        continue;
                    }
                    if(preg_match('#^'.preg_quote($taxName).'(-([0-9]+))?$#',$query,$match)){

                        //print_r($match);
                        $xml .= self::content_taxonomy($taxName,$match[2]);
                        $match = true;
                        break;
                    }
                }


                break;
        }








        $xml .= self::footer('url');


        return $xml;
    }

    private static function content_misc($param){
        $blog_update = get_lastpostmodified('blog');


        $cnf = self::cnf('sitemap_seo.content_item.index');


	    $xml = self::add_url(home_url(),$blog_update,$cnf['frequency'],$cnf['weights']);



	    return $xml;
    }

    private static function content_taxonomy($taxonomy,$param){


        $cnf = self::cnf('sitemap_seo.content_item.'.$taxonomy);

        //print_r([$taxonomy,$param]);
	    global $wpdb;

	    $sql = "SELECT
				MAX(p.post_modified) as _mod_date
			FROM
				{$wpdb->posts} p,
				{$wpdb->term_relationships} r
			WHERE
				p.ID = r.object_id
				AND p.post_status = 'publish'
				AND p.post_password = ''
				AND r.term_taxonomy_id = %d";


	    //print_r($param);
        $query_args = array("hide_empty" => true, "hierarchical" => false,'number'=>self::$pagesize,'offset'=>0);
	    if(preg_match('#^\d+$#i',$param)){
            $query_args['offset'] = $param * self::$pagesize;
        }

	    //print_r($query_args);

        $terms = get_terms($taxonomy, $query_args);
        $xml = '';
        foreach($terms AS $term) {
            $last_mod = $wpdb->get_var($wpdb->prepare($sql,$term->term_taxonomy_id));
            $xml .= self::add_url(get_term_link($term, $term->taxonomy), $last_mod, $cnf['frequency'],$cnf['weights']);
        }


	    return $xml;
    }

    private static function content_post($post_type,$param){

	    global $wpdb;

        $cnf = self::cnf('sitemap_seo.content_item.'.$post_type);

	    $xml = '';
	    $where = '';


	    //print_r([$post_type,$param]);

	    $limit = '';
	    if($param){
	        if(preg_match('#^(\d+)-(\d+)$#',$param,$m)){
	            $year = $m[1];
                $offset = $m[2] * self::$pagesize;
                $limit = ' LIMIT '.$offset.','.self::$pagesize;
            }else{
                $year = $param;
                $limit = ' LIMIT 0,'.self::$pagesize;
            }
            $where = $wpdb->prepare(" AND YEAR(p.post_date) = %d",$year);
        }
        $sql = "SELECT
					p.ID,
					p.post_author,
					p.post_status,
					p.post_name,
					p.post_parent,
					p.post_type,
					p.post_date,
					p.post_date_gmt,
					p.post_modified,
					p.post_modified_gmt,
					p.comment_count
				FROM
					{$wpdb->posts} p
				WHERE
					p.post_password = ''
					AND p.post_type = '%s'
					AND p.post_status = 'publish'
					$where
				ORDER BY
					p.post_date DESC ".$limit;


        $list = $wpdb->get_results($wpdb->prepare($sql,$post_type));

        foreach($list as $post){
            $url = get_permalink($post);

            $xml .= self::add_url($url,$post->post_modified,$cnf['frequency'],$cnf['weights']);
        }

        return $xml;
    }

    public static function content_author(){
	    global $wpdb;
        $xml = '';
        $cnf = self::cnf('sitemap_seo.content_item.author');
        $sitemap_cnf = self::cnf('sitemap_seo.content_item');
        $post_types = self::post_types();


        $query_type = array();

        foreach($post_types as $type=>$term){
            if(isset($sitemap_cnf[$type]) && $sitemap_cnf[$type]['switch']){
                $query_type[] = $type;
            }
        }
        if(empty($query_type)){
            return $xml;
        }

        $types = implode("','",$query_type);

        $sql = "SELECT DISTINCT
					u.ID,
					u.user_nicename,
					MAX(p.post_modified) AS last_mod
				FROM
					{$wpdb->users} u,
					{$wpdb->posts} p
				WHERE
					p.post_author = u.ID
					AND p.post_status = 'publish'
					AND p.post_type IN('$types')
					AND p.post_password = ''
				GROUP BY
					u.ID";

        $list = $wpdb->get_results($sql);

        foreach($list as $author) {
            $url = get_author_posts_url($author->ID, $author->user_nicename);
            $xml .= self::add_url($url, $author->last_mod, $cnf['frequency'],$cnf['weights']);
        }

        return $xml;
    }

    public static function content_archives(){
        global $wpdb;

        $xml = '';
        $cnf = self::cnf('sitemap_seo.content_item.archive');

        $sitemap_cnf = self::cnf('sitemap_seo.content_item');

        $post_types = self::post_types();

        $query_type = array();

        foreach($post_types as $type=>$term){
            if(isset($sitemap_cnf[$type]) && $sitemap_cnf[$type]['switch']){
                $query_type[] = $type;
            }
        }
        if(empty($query_type)){
            return $xml;
        }

        $types = implode("','",$query_type);

        $list = $wpdb->get_results("
			SELECT DISTINCT
				YEAR(post_date) AS `year`,
				MONTH(post_date) AS `month`,
				MAX(post_date) AS last_mod,
				count(ID) AS posts
			FROM
				$wpdb->posts
			WHERE
				post_status = 'publish'
				AND post_type IN('$types') 
				AND post_password = ''
			GROUP BY
				YEAR(post_date),
				MONTH(post_date)
			ORDER BY
				post_date DESC");

        foreach($list as $archive) {

            $url = get_month_link($archive->year, $archive->month);

            $xml .= self::add_url($url, $archive->last_mod, $cnf['frequency'],$cnf['weights']);
        }


        return $xml;
    }

    public static function last_mod($date){

	    $time = strtotime($date);
	    return date('c',$time);

    }

    public static function add_url($url,$last_mod,$fr,$pr){

        $xml = '<url>';
        $xml .= '    <loc>'.$url.'</loc>';
        $xml .= '    <lastmod>'.self::last_mod($last_mod).'</lastmod>';
        $xml .= '    <changefreq>'.$fr.'</changefreq>';
        $xml .= '    <priority>'.$pr.'</priority>';
        $xml .= '</url>';

        return $xml;
    }


    public static function add_sitemap($url,$last_mod){


	    $xml = '<sitemap>';
	    $xml .= '    <loc>'.home_url('/sitemap-'.$url.'.xml').'</loc>';
	    $xml .= '    <lastmod>'.self::last_mod($last_mod).'</lastmod>';
	    $xml .= '</sitemap>';

	    return $xml;
    }

    public static function header($type){
	    $xml =  '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
	    $xml .= '<' . '?xml-stylesheet type="text/xsl" href="' . esc_url( plugin_dir_url(SMART_SEO_TOOL_BASE_FILE).'assets/sitemap.xsl' ) . '"?' . '>';
	    if($type == 'url'){
	        $xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        }else{

            $xml .= '<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        }

        return $xml;
    }


    public static function footer($type){


	    if($type=='url'){
	        $xml = '</urlset>';
        }else{
	        $xml = '</sitemapindex>';
        }

        return $xml;

    }





}