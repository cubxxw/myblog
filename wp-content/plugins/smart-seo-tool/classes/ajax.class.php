<?php
/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com/
 */

class Smart_SEO_Tool_Ajax
{

    public static function init()
    {
        add_action('wp_ajax_wb_smart_seo_tool', array(__CLASS__, 'wp_ajax_wb_smart_seo_tool'));
    }

    public static function wp_ajax_wb_smart_seo_tool()
    {
        global $wpdb;
        $ret = array('code'=>0,'desc'=>'success');

        $op = isset($_REQUEST['op'])?sanitize_text_field($_REQUEST['op']):null;
        switch ($op) {
            case 'chk_ver':
                if( !current_user_can('manage_options')) {
                    exit();
                }
                $api = 'https://www.wbolt.com/wb-api/v1/themes/checkver?code=' . SMART_SEO_TOOL_CODE . '&ver=' . SMART_SEO_TOOL_VERSION . '&chk=1';
                $http = wp_remote_get($api, array('sslverify' => false, 'headers' => array('referer' => home_url()),));
                if (wp_remote_retrieve_response_code($http) == 200) {
                    echo wp_remote_retrieve_body($http);
                }
                exit();
                break;

            case 'doc':
				$ret['data'] = Smart_SEO_Tool_Admin::$cnf_fields['variables_desc'];
                break;

            case '404_url':
                if( !current_user_can('manage_options')) {
                    $ret['success'] = 1;
                    $ret['data'] = [];
                    break;
                }
                $num = 30;

                $offset = 0;
                if(isset($_GET['page'])){
                    $page = intval(sanitize_text_field($_GET['page']));
                    $page = max(1,$page);
                    $offset = ($page - 1 ) * $num;
                }else if(isset($_GET['offset'])){
                    $offset = intval(sanitize_text_field($_GET['offset']));
                }

                /*$offset = isset($_GET['offset'])?intval(sanitize_text_field($_GET['offset'])):0;
                $offset = max(0,$offset);
                $num = 30;*/
                $url_log = $wpdb->prefix.'wb_spider_log';
                $list = $wpdb->get_results("SELECT * FROM $url_log WHERE `code`=404 ORDER BY id DESC LIMIT $offset,$num");

                $ret['success'] = 1;
                $ret['data'] = $list;
                break;

            case 'remove_404':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                $id = isset($_POST['id'])?absint(sanitize_text_field($_POST['id'])):0;
                if(!$id){
                    $ret['data'] = 'error';
                    break;
                }
                $t = $wpdb->prefix.'wb_spider_log';
                $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id=%d",$id));
                if(!$row){
                    $ret['data'] = 'error';
                    break;
                }
                $ret['d'] = array('code'=>404,'url_md5'=>$row->url_md5);
                $wpdb->delete($t,array('code'=>404,'url_md5'=>$row->url_md5));
                $ret['data'] = 'success';//$ret?'success':'fail';

                break;

            case 'refresh_404':
                $ret['success'] = 0;
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                $id = isset($_POST['id'])?absint(sanitize_text_field($_POST['id'])):0;
                if(!$id){
                    $ret['data'] = 'error';
                    break;
                }
                $t = $wpdb->prefix.'wb_spider_log';
                $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id=%d",$id));
                if(!$row){
                    $ret['data'] = 'error';
                    break;
                }

                $req_url = home_url($row->url);
                $http = wp_remote_head($req_url);
                if(is_wp_error($http)){
                    $ret['data'] = $http->get_error_message();
                    break;
                }
                $http_code = wp_remote_retrieve_response_code($http);
                $ret['data'] = $http_code;
                if($http_code && $row->code != $http_code){
                    $wpdb->update($wpdb->prefix.'wb_spider_log',['visit_date'=>current_time('mysql'),'code'=>$http_code],['id'=>$row->id]);
                    $wpdb->update($wpdb->prefix.'wb_spider_log',['code'=>$http_code],['url_md5'=>$row->url_md5]);
                }
                $ret['success'] = 1;
                break;

            case 'mark_broken_url':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                $id = isset($_POST['id'])?absint(sanitize_text_field($_POST['id'])):0;
                if(!$id){
                    $ret['data'] = 'error';
                    break;
                }
                Smart_SEO_Tool_Common::mark_broken_url($id);
                $ret['data'] = 'success';
                break;

            case 'remove_broken':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                $id = isset($_POST['id'])?absint(sanitize_text_field($_POST['id'])):0;
                if(!$id){
                    $ret['data'] = 'error';
                    break;
                }
                Smart_SEO_Tool_Common::remove_broken_url($id);
                $ret['data'] = 'success';//$ret?'success':'fail';

                break;

            case 'check_broken':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error1';
                    break;
                }
                $id = isset($_POST['id'])?absint(sanitize_text_field($_POST['id'])):0;
                if(!$id){
                    $ret['data'] = 'error2';
                    break;
                }
                $row = Smart_SEO_Tool_Common::detect_url($id);
                if(!$row){
                    $ret['data'] = 'error3';
                    //$ret['row'] = $row;
                    break;
                }
                $ret['row'] = $row;
                $ret['data'] = $row->code;

                break;

            case 'clear_broken_url':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                Smart_SEO_Tool_Common::clear_broken_url();
                break;

            case 'broken_url_batch':
                if( !current_user_can('manage_options')) {
                    $ret['data'] = 'error';
                    break;
                }
                $ret['success'] = 0;
                $ids = isset($_POST['ids'])?trim(sanitize_text_field($_POST['ids'])):'';
                $op = isset($_POST['op'])?trim(sanitize_text_field($_POST['op'])):'';
                if(!$ids || !$op){
                    $ret['data'] = 'error';
                    break;
                }
                $t = $wpdb->prefix.'wb_sst_broken_url';
                $ids = preg_replace('#[^\d,]#','',$ids);
                if($op == 'update'){
                    $wpdb->query("UPDATE $t SET check_date = null,code= null WHERE id IN($ids)");
                    $ret['success'] = 1;
                }else if($op == 'ok'){
                    $wpdb->query("UPDATE $t SET check_date = '2023-10-01 10:00:00',code= 200,memo='mark as ok' WHERE id IN($ids)");
                    $ret['success'] = 1;
                }else if($op == 'cancel'){
                    $id_list = explode(',',$ids);
                    foreach($id_list as $id){
                        if(!$id){
                            continue;
                        }
                        Smart_SEO_Tool_Common::remove_broken_url($id);
                    }
                    $ret['success'] = 1;
                }

                break;

            case 'broken_url':
                if( !current_user_can('manage_options')) {
                    $ret['success'] = 1;
                    $ret['data'] = [];
                    break;
                }
                $num = 30;

                $offset = 0;
                if(isset($_GET['page'])){
                    $page = intval(sanitize_text_field($_GET['page']));
                    $page = max(1,$page);
                    $offset = ($page - 1 ) * $num;
                }else if(isset($_GET['offset'])){
                    $offset = intval(sanitize_text_field($_GET['offset']));
                }

                $type = isset($_GET['type'])?intval(sanitize_text_field($_GET['type'])):0;
                $offset = max(0,$offset);

                $url_log = $wpdb->prefix.'wb_sst_broken_url';
                $where = '';
                if($type==2){
                    $where = " AND CODE REGEXP '^30'";
                }else if($type == 1){
                    $where = " AND CODE REGEXP '^(5|4|error)'";
                }else if($type == 3){
                    $where = " AND CODE REGEXP '^2'";
                }
                $list = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM $url_log WHERE url_md5 IS NOT NULL $where ORDER BY id DESC LIMIT $offset,$num");
                $record_total = $wpdb->get_var("SELECT FOUND_ROWS()");
                if($list)foreach($list as $r){
                    if(!$r->code){
                        $r->code = '待检测';
                    }
                    $post = get_post($r->post_id);
                    $r->post_title = $post->post_title;
                    $r->post_url = get_permalink($post);
                    $r->edit_url = get_edit_post_link($post,'');
                }

                $ret['success'] = 1;
                $ret['data'] = $list;
                $ret['page_num'] = $num;
                $ret['record_total'] = $record_total;

                break;
            case 'robots_txt':
                //$ret['data'] = array();
                $output = "User-agent: *".PHP_EOL;
                $site_url = parse_url(site_url());
                $path = (!empty($site_url['path'])) ? $site_url['path'] : '';
                $output .= "Disallow: $path/wp-admin/".PHP_EOL;
                $output .= "Disallow: $path/wp-include/".PHP_EOL;
                $output .= "Disallow: $path/wp-login.php?redirect_to=*".PHP_EOL;
                $output .= "Disallow: $path/go?_=*".PHP_EOL;
                $output .= "Allow: $path/wp-admin/admin-ajax.php".PHP_EOL;

                $ret['data'] = $output;

                break;

	        case 'get_options':
                $ret['data'] = array();
                if( !current_user_can('manage_options')) {
                    break;
                }
		        if( isset($_POST['key']) ){
			        $ret['data'] = Smart_SEO_Tool_Admin::get_setting(sanitize_text_field($_POST['key']));
		        }

		        if( isset($_POST['type']) ){
			        $keys = explode(',', sanitize_text_field($_POST['type']));
			        $ret['data']['title_variables'] = array();

			        if(count($keys)>1){
				        foreach ($keys as $k){
					        $ret['data']['title_variables'][$k] = Smart_SEO_Tool_Admin::get_title_variables($k);
				        }
			        }else{
			        	$key = sanitize_text_field($_POST['type']);
				        $ret['data']['title_variables'][$key] = Smart_SEO_Tool_Admin::get_title_variables($key);
			        }
		        }
		        $ret['type'] = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

	        	break;

	        case 'update_options':
                if( !current_user_can('manage_options')) {
                    break;
                }

		        if( isset($_POST['key']) && isset($_POST['opt']) ){

			        Smart_SEO_Tool_Admin::update_cnf();
		        }
	        	break;
            case 'update_active':
                if( !current_user_can('manage_options')) {
                    break;
                }
                if(isset($_POST['opt']) ){

                    Smart_SEO_Tool_Admin::update_active();
                }
                break;
            case 'get_guide':
                $ret['data'] = array();
                if( !current_user_can('manage_options')) {
                    break;
                }

                $cnf = Smart_SEO_Tool_Admin::guide_cnf();

                $ret['data'] = $cnf;
                $ret['cnf'] = [
                    'separator'=>Smart_SEO_Tool_Admin::$cnf_fields['separator'],
                    'title_variables'=>Smart_SEO_Tool_Admin::get_title_variables('common'),
                ];


                break;
            case 'set_guide':
                if( !current_user_can('manage_options')) {
                    break;
                }
                Smart_SEO_Tool_Admin::set_guide();

                break;
            case 'start_guide':
                if( !current_user_can('manage_options')) {
                    break;
                }
                Smart_SEO_Tool_Admin::start_guide();

                break;
        }

        header('content-type:text/json;charset=utf-8');
        echo json_encode($ret);
        exit();

    }
}