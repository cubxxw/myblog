<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hashbar_Countdown{

	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Actions]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(){
		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Load attributes from block.json.
		ob_start();
		include HASHBAR_BLOCK_PATH . '/src/block/hashbar-countdown/block.json';
		$attributes = json_decode( ob_get_clean(), true );

		register_block_type(
			'hashbar/hashbar-countdown',
			array(
				'attributes'  	  => $attributes,
				'render_callback' => [ $this, 'render_content' ],
			)
		);

	}

	public function render_content($attr){
		$block_id = get_the_id();
		$attr['countdown_timr_box_width'] = (string)$attr['countdown_timr_box_width'];
		$attr['countdown_box_height']     = $attr['countdown_box_height'] > 0 ? (string)$attr['countdown_box_height'] : "";
		$attr['countdown_box_width'] 	  = $attr['countdown_box_width'] > 0 ? (string)$attr['countdown_box_width']  : "";
		ob_start();
		echo '<div id="hthb-countdown-block-'.$block_id.'" class="hthb-countdown">';
			echo hashbar_do_shortcode('hashbar_countdown',$attr);
		echo '</div>';
		?>
			<style type="text/css">
				#hthb-countdown-block-<?php echo $block_id; ?>.hthb-countdown{
					text-align: <?php echo $attr['countDownPosition']; ?>;
				}
				#hthb-countdown-block-<?php echo $block_id; ?>.hthb-countdown .hthb-single-countdown{
					border: <?php echo $attr['countdown_timr_border_width'];?>px <?php echo $attr['countdown_timr_border'];?>  <?php echo $attr['countdown_timr_border_color'];?>;
					border-radius: <?php echo $attr['timerBorderRadius']['top'];?><?php echo $attr['timerBorderRadius']['unit'];?> <?php echo $attr['timerBorderRadius']['right'];?><?php echo $attr['timerBorderRadius']['unit'];?> <?php echo $attr['timerBorderRadius']['bottom'];?><?php echo $attr['timerBorderRadius']['unit'];?> <?php echo $attr['timerBorderRadius']['left'];?><?php echo $attr['timerBorderRadius']['unit'];?>;
					padding: <?php echo $attr['timerPadding']['top'];?><?php echo $attr['timerPadding']['unit'];?> <?php echo $attr['timerPadding']['right'];?><?php echo $attr['timerPadding']['unit'];?> <?php echo $attr['timerPadding']['bottom'];?><?php echo $attr['timerPadding']['unit'];?> <?php echo $attr['timerPadding']['left'];?><?php echo $attr['timerPadding']['unit'];?>;
				}
				#hthb-countdown-block-<?php echo $block_id; ?>.hthb-countdown .hthb-single-countdown__time{
					border: <?php echo $attr['countdown_number_border_width'];?>px <?php echo $attr['countdown_number_border'];?>  <?php echo $attr['countdown_number_border_color'];?>;
					font-size: <?php echo $attr['countdown_timr_font_size']; ?>;
					border-radius: <?php echo $attr['numberBorderRadius']['top'];?><?php echo $attr['numberBorderRadius']['unit'];?> <?php echo $attr['numberBorderRadius']['right'];?><?php echo $attr['numberBorderRadius']['unit'];?> <?php echo $attr['numberBorderRadius']['bottom'];?><?php echo $attr['numberBorderRadius']['unit'];?> <?php echo $attr['numberBorderRadius']['left'];?><?php echo $attr['numberBorderRadius']['unit'];?>;
					padding: <?php echo $attr['numberPadding']['top'];?><?php echo $attr['numberPadding']['unit'];?> <?php echo $attr['numberPadding']['right'];?><?php echo $attr['numberPadding']['unit'];?> <?php echo $attr['numberPadding']['bottom'];?><?php echo $attr['numberPadding']['unit'];?> <?php echo $attr['numberPadding']['left'];?><?php echo $attr['numberPadding']['unit'];?>;
				}
				#hthb-countdown-block-<?php echo $block_id; ?>.hthb-countdown .hthb-single-countdown__text{
					border: <?php echo $attr['countdown_label_border_width'];?>px <?php echo $attr['countdown_label_border'];?>  <?php echo $attr['countdown_label_border_color'];?>;
					font-size: <?php echo $attr['countdownLabelFontSize']; ?>;
					border-radius: <?php echo $attr['labelBorderRadius']['top'];?><?php echo $attr['labelBorderRadius']['unit'];?> <?php echo $attr['labelBorderRadius']['right'];?><?php echo $attr['labelBorderRadius']['unit'];?> <?php echo $attr['labelBorderRadius']['bottom'];?><?php echo $attr['labelBorderRadius']['unit'];?> <?php echo $attr['labelBorderRadius']['left'];?><?php echo $attr['labelBorderRadius']['unit'];?>;
					padding: <?php echo $attr['labelPadding']['top'];?><?php echo $attr['labelPadding']['unit'];?> <?php echo $attr['labelPadding']['right'];?><?php echo $attr['labelPadding']['unit'];?> <?php echo $attr['labelPadding']['bottom'];?><?php echo $attr['labelPadding']['unit'];?> <?php echo $attr['labelPadding']['left'];?><?php echo $attr['labelPadding']['unit'];?>;
				}
			</style>
		<?php
		return ob_get_clean();
	}

}

Hashbar_Countdown::instance();