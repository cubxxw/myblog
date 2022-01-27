<?php
/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com/
 */

class Smart_SEO_Tool_Images {


    private $content = '%title%';
    private $thumb = '%title%';
    private $type = 1;
    private $mode = 1;

	public function __construct(){

	    $this->content = Smart_SEO_Tool_Common::cnf('img_seo.content');
	    $this->thumb = Smart_SEO_Tool_Common::cnf('img_seo.thumb');
	    $this->type = Smart_SEO_Tool_Common::cnf('img_seo.active');
	    $this->mode = Smart_SEO_Tool_Common::cnf('img_seo.mode');

	    if($this->type){
	        if($this->content){
                add_filter( 'the_content', array( $this, 'handel_images' ), 500 );
            }
	        if($this->thumb){
                add_filter( 'post_thumbnail_html', array( $this, 'handel_images_featured' ), 500 ,2);
            }
        }
	}

	public function img_attr($attr,$search){

	    $content = $this->content;
	    foreach($search as $k=>$v){
            $content = str_replace('%'.$k.'%',$v,$content);
        }
	    return ' '.$attr.'="'.esc_attr($content).'"';
    }

    public function thumb_attr($attr,$search){

        $content = $this->thumb;
        foreach($search as $k=>$v){
            $content = str_replace('%'.$k.'%',$v,$content);
        }
        return ' '.$attr.'="'.esc_attr($content).'"';
    }


	public function handel_images( $content ) {
        if(!preg_match_all('#<img([^>]+)>#is',$content,$match)){
            return $content;
        }

        $search = array('site_title'=>'','img_name'=>'','title'=>'','post_cat'=>'','num'=>'');
        if(preg_match('#%site_title%#',$this->content)){
            $search['site_title'] = get_bloginfo('name', 'display');
        }
        if(preg_match('#%title%#',$this->content)){
            $search['title'] = get_the_title();
        }
        if(preg_match('#%post_cat%#',$this->content)){
            $cate = get_the_category();
            $post_cat = array();
            if($cate){
                foreach($cate as $term){
                    $post_cat[] = $term->name;
                }
            }
            $search['post_cat'] = implode('、',$post_cat);
        }

        $img_seo = array();
        $img_idx = -1;
        foreach($match[1] as $k=>$img){
            if(!preg_match('#src=.+#',$img)){
                continue;
            }
            $img = trim($img,'/');

            $src_img = $match[0][$k];//$img;
            $img = str_replace(array('alt=""',"alt=''",'title=""',"title=''"),'',$img);

            $img_key = md5($img);

            if(!isset($img_seo[$img_key])){
                $img_idx ++;
                $img_seo[$img_key] = $img_idx;
            }
            $img_k = $img_seo[$img_key];

            $add_html = '';
            if($this->mode == 2){
                $img = preg_replace('#\s+title="[^"]+"#is','',$img);
                $img = preg_replace('#\s+title=\'[^\']+\'#is','',$img);
                $img = preg_replace('#\s+alt="[^"]+"#is','',$img);
                $img = preg_replace('#\s+alt=\'[^\']+\'#is','',$img);
            }
            $search['num'] = $img_k?$img_k:'';//'('.$img_k.')'

            //empty title
            if(!preg_match('#\s+title=.+?#is',$img)){
                $search['img_name'] = '';
                if(preg_match('#/.+?\.(jpg|jpeg|gif|webp|png|bmp)#is',$img,$name_match)){
                    $search['img_name'] = basename($name_match[0]);
                }
                $add_html .= $this->img_attr('title',$search);
            }
            //empty alt
            if(!preg_match('#\s+alt=.+?#is',$img)){
                $search['img_name'] = '';
                if(preg_match('#/.+?\.(jpg|jpeg|gif|webp|png|bmp)#is',$img,$name_match)){
                    //.esc_attr(basename($name_match[0])).'插图'.($img_k?'('.$img_k.')':'').'"';
                    $search['img_name'] = basename($name_match[0]);
                }
                $add_html .= $this->img_attr('alt',$search);
            }
            if(!$add_html){
                continue;
            }


            //$original = str_replace(array('alt=""',"alt=''",'title=""',"title=''"),'',$match[1][$k]);

            $img = trim($img);
            $new_img = '<img '.$img.$add_html.' />';
            $content = str_replace($src_img,$new_img,$content);
        }//end foreach match

		return $content;
	}

    public function handel_images_featured( $html,$post_id ) {


	    if(!preg_match('#<img([^>]+)>#i',$html,$match)){
	        return $html;
        }//end if preg_match image

        $search = array('site_title'=>'','img_name'=>'','title'=>'','post_cat'=>'','num'=>'');
        if(preg_match('#%site_title%#',$this->thumb)){
            $search['site_title'] = get_bloginfo('name', 'display');
        }
        if(preg_match('#%title%#',$this->thumb)){
            $search['title'] = get_the_title($post_id);
        }
        if(preg_match('#%post_cat%#',$this->thumb)){
            $cate = get_the_category($post_id);
            $post_cat = array();
            if($cate){
                foreach($cate as $term){
                    $post_cat[] = $term->name;
                }
            }
            $search['post_cat'] = implode('、',$post_cat);
        }


        $img = trim($match[1],'/');

        if(!preg_match('#src=.+#',$img)){
            return $html;
        }

        $add_html = '';
        //$post_title = get_the_title($post_id);
        $img = str_replace(array('alt=""',"alt=''",'title=""',"title=''"),'',$img);

        if($this->mode == 2){
            $img = preg_replace('#\s+title="[^"]"#is','',$img);
            $img = preg_replace('#\s+title=\'[^\']\'#is','',$img);
            $img = preg_replace('#\s+alt="[^"]"#is','',$img);
            $img = preg_replace('#\s+alt=\'[^\']\'#is','',$img);
        }

        if(!preg_match('#\s+title=.+?#is',$img)){
            $search['img_name'] = '';
            if(preg_match('#/.+?\.(jpg|jpeg|gif|webp|png|bmp)#is',$img,$name_match)){
                $search['img_name'] = basename($name_match[0]);
            }
            $add_html .= $this->thumb_attr('title',$search);
        }
        if(!preg_match('#\s+alt=.+?#is',$img)){
            $search['img_name'] = '';
            if(preg_match('#/.+?\.(jpg|jpeg|gif|webp|png|bmp)#is',$img,$name_match)){
                $search['img_name'] = basename($name_match[0]);
            }
            $add_html .= $this->thumb_attr('alt',$search);
        }
        if($add_html){
            $img = trim($img);
            $html = '<img '.$img.$add_html.' />';
        }


	    return $html;

	}	

}