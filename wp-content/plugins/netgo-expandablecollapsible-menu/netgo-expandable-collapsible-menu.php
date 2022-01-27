<?php
/*
Plugin Name: NetGo Expandable/Collapsible Menu
Plugin URI: http://www.netattingo.com/
Description: This plugin adds a widget that makes your 'all page list' or 'menu' with expandable and collapsible effect.
Author: NetAttingo Technologies
Version: 1.0.0
Author URI: http://www.netattingo.com/
*/

add_action('widgets_init', create_function('', 'return register_widget("Netgo_Navigation");'));
add_action("init", "netgo_navigation_init");

function netgo_navigation_init() {

	define( "NETGO_NAVIGATION_URL",  plugins_url() . '/netgo-expandablecollapsible-menu/include/');
	define( "NETGO_NAVIGATION_VERSION", "1.0");
	
	// Add more stlyes to the output of wp_list_pages
	add_filter('page_css_class', 'netgo_navigation_page_css_class', 10, 4);

	// Add scripts and styles to the front end
	add_action('wp_enqueue_scripts', 'netgo_navigation_enqueue_scripts');

}

function netgo_navigation_enqueue_scripts() {
	wp_enqueue_script( "netgo-expandable-collapsible-menu", NETGO_NAVIGATION_URL . "script.js", array("jquery"), NETGO_NAVIGATION_VERSION );	
	wp_enqueue_style( "netgo-expandable-collapsible-menu", NETGO_NAVIGATION_URL . "styles.css", array(), NETGO_NAVIGATION_VERSION, "screen" );
}

class Netgo_Navigation extends WP_Widget {

	var $arr_types = array(
		"wp_list_pages" => array("function" => "wp_list_pages", "name" => "List all pages" ),
		//"wp_list_categories" => array("function" => "wp_list_categories", "name" => "List all categories"),
		
	);
	
	
	function Netgo_Navigation() {
		parent::WP_Widget('netgo_navigation', 'Netgo Expandable/Collapsible Menu', array('description' => 'Outputs pages/categories with expandable/collapsible effect.', 'class' => 'netgo-navigation-class'));	
	}

	// outputs the options form on admin
	function form($instance) {
    
		// Title
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		$title_value = esc_html($instance["title"]);
		echo "<p>";
		echo "<label for='$field_id'>Title</label>";
		echo "<input class='widefat' type='text' value='$title_value' name='$field_name' id='$field_id' />";
		echo "</p>";

		// Select as menu
		$field_id = $this->get_field_id('function');
		$field_name = $this->get_field_name('function');
		echo "<p>";
		echo "<label for='$field_id'>Select as Menu</label>";
		echo "<select id='$field_id' name='$field_name' class='widefat'>";
		foreach ($this->arr_types as $type) {
			$selected = "";
			if ($instance["function"] == $type["function"]) {
				$selected = ' selected="selected" ';
			}
			echo "<option $selected value='{$type["function"]}'>{$type["name"]}</option>";
		}
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		foreach ( $menus as $menu ){
		$selected = "";
			if ($instance["function"] == $menu->name) {
				$selected = ' selected="selected" ';
		}	
			
		echo "<option $selected value='{$menu->name}'>{$menu->name}</option>";	
		}
		echo "</select>";
		echo "</p>";
		
		?>
	
		<?php
		
				
	}

	function update($new_instance, $old_instance) {

		// processes widget options to be saved
		// fill current state with old data to be sure we not loose anything

		$instance = $old_instance;
		
		$instance["title"] = $new_instance["title"];
		$instance["function"] = $new_instance["function"];
		
		
		return $instance;
	}

	// outputs the content of the widget
	function widget($args, $instance) {

		
		$widget_id = "netgo_navigation_" . $args["widget_id"];
		$widget_id_for_js = str_replace("-", "", $widget_id);
		
		?>
		<script type="text/javascript">
			var netgo_navigation_options = netgo_navigation_options || {};
			netgo_navigation_options.<?php echo $widget_id_for_js ?> = {
				clickable_parent: 	<?php echo $instance["clickable_parent"] ? "true" : "false"; ?>,
				widget_id: 			"<?php echo $widget_id ?>"
			}
		</script>
		<?php
		

		$nav_output = "";

		$nav_output .= $args["before_widget"];
		$nav_output .= $args["before_title"];
		$nav_output .= $instance["title"];
		$nav_output .= $args["after_title"];
		
		$function = $instance["function"];
		$arguments .= "&title_li=0&echo=1";
		
		$look = $instance["look"];

		

		if ($function != '') {
			$nav_output .= "<div class='netgo_navigation' id='{$widget_id}'>";

		    if ($function == "wp_list_pages" or $function == "wp_list_categories") {
				$nav_output .= "<ul>";
		    }
			ob_start();
			if ($function == "wp_list_pages" or $function == "wp_list_categories") {
			call_user_func($function, $arguments);
			 
			
			}else{
			wp_nav_menu( array('menu' => $function ));	
			} 
			$nav_output .= ob_get_clean();
			
			if ($function == "wp_list_pages" or $function == "wp_list_categories") {
				$nav_output .= "</ul>";
			}
			$nav_output .= "</div>";
		} else {
			$nav_output .= "<p>Could not find function '$function'.";
		}
		
		$nav_output .= $args["after_widget"];
	
		echo $nav_output;

	}

}


/**
 * adds class "page-has-children" to all pages that have children
 * @param array $class.	The page css class being modified, passed as an array from Walker_Page
 * @param object $page.	The page object passed from Walker_Page
 * @return array			Returns the new page css class.
 */
function netgo_navigation_page_css_class($class, $page, $depth, $args) {


	// check if current page has children and add class if it does
	if ($args["has_children"]) {
		$class[] = "page-has-children";
	}

	return $class;
 
}




