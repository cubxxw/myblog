<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMega_Elementor_Widget_Instagram extends Widget_Base {

    public function get_name() {
        return 'htmega-instagram-addons';
    }
    
    public function get_title() {
        return __( 'Instagram', 'htmega-addons' );
    }

    public function get_icon() {
        return 'htmega-icon eicon-photo-library';
    }

    public function get_categories() {
        return [ 'htmega-addons' ];
    }

    public function get_style_depends() {
        return [
            'elementor-icons-shared-0-css','elementor-icons-fa-brands','elementor-icons-fa-regular','elementor-icons-fa-solid',
            'slick',
        ];
    }

    public function get_script_depends() {
        return [
            'htmegainstagramfeed',
            'slick',
            'htmega-widgets-scripts',
        ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'instagram_content',
            [
                'label' => __( 'Instagram', 'htmega-addons' ),
            ]
        );
        
            $this->add_control(
                'instagram_style',
                [
                    'label' => __( 'Style', 'htmega-addons' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '1',
                    'options' => [
                        '1'   => __( 'Style One', 'htmega-addons' ),
                        '2'   => __( 'Style Two', 'htmega-addons' ),
                        '3'   => __( 'Style Three', 'htmega-addons' ),
                        '4'   => __( 'Style Four', 'htmega-addons' ),
                    ],
                    'label_block' => true,
                ]
            );

            $this->add_control(
                'username',
                [
                    'label'         => __( 'Instagram UserName', 'htmega-addons' ),
                    'type'          => Controls_Manager::TEXT,
                    'placeholder'   => __( 'portfolio.devitems', 'htmega-addons' ),
                    'label_block'   =>true,
                ]
            );

            $this->add_control(
                'limit',
                [
                    'label' => __( 'Item Limit', 'htmega-addons' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 200,
                    'step' => 1,
                    'default' => 8,
                    'separator'=>'before',
                ]
            );

            $this->add_responsive_control(
                'instagram_column',
                [
                    'label' => __( 'Column', 'htmega-addons' ),
                    'type' => Controls_Manager::SELECT,
                    'description'   => wp_kses_post( 'If the slider is off, Then it will work.', 'htmega-addons' ),
                    'prefix_class' => 'htmegainstagram-column%s-',
                    'default' => '4',
                    'required' => true,
                    'device_args' => [
                        Controls_Stack::RESPONSIVE_TABLET => [
                            'required' => false,
                        ],
                        Controls_Stack::RESPONSIVE_MOBILE => [
                            'required' => false,
                        ],
                    ],
                    'min_affected_device' => [
                        Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
                        Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
                    ],
                    'options' => [
                        '1'   => __( '1', 'htmega-addons' ),
                        '2'   => __( '2', 'htmega-addons' ),
                        '3'   => __( '3', 'htmega-addons' ),
                        '4'   => __( '4', 'htmega-addons' ),
                        '5'   => __( '5', 'htmega-addons' ),
                        '6'   => __( '6', 'htmega-addons' ),
                    ],
                ]
            );

            $this->add_control(
                'instagram_image_size',
                [
                    'label' => __( 'Image Size', 'htmega-addons' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '480',
                    'options' => [
                        '150'   => __( '150', 'htmega-addons' ),
                        '240'   => __( '240', 'htmega-addons' ),
                        '320'   => __( '320', 'htmega-addons' ),
                        '480'   => __( '480', 'htmega-addons' ),
                        '640'   => __( '640', 'htmega-addons' ),
                    ],
                ]
            );

            $this->add_control(
                'clear_cache_data',
                [
                    'label'         => esc_html__( 'Clear Cache Data', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Yes', 'htmega-addons' ),
                    'label_off'     => esc_html__( 'No', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                ]
            );

            $this->add_control(
                'lazy_load',
                [
                    'label'         => __( 'Lazy  Load', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'Show', 'htmega-addons' ),
                    'label_off'     => __( 'Hide', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                ]
            );

            $this->add_control(
                'show_like',
                [
                    'label'         => __( 'Show Like', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'Show', 'htmega-addons' ),
                    'label_off'     => __( 'Hide', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'show_comment',
                [
                    'label'         => __( 'Show Comment', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'Show', 'htmega-addons' ),
                    'label_off'     => __( 'Hide', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'show_light_box',
                [
                    'label'         => __( 'Show Light Box', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'Show', 'htmega-addons' ),
                    'label_off'     => __( 'Hide', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'show_flow_button',
                [
                    'label'         => __( 'Show Follow Button', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'Show', 'htmega-addons' ),
                    'label_off'     => __( 'Hide', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'yes',
                ]
            );

            $this->add_control(
                'slider_on',
                [
                    'label'         => __( 'Slider', 'htmega-addons' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'label_on'      => __( 'On', 'htmega-addons' ),
                    'label_off'     => __( 'Off', 'htmega-addons' ),
                    'return_value'  => 'yes',
                    'default'       => 'no',
                ]
            );

            $this->add_control(
                'zoomicon_type',
                [
                    'label' => esc_html__('Zoom Icon Type','htmega-addons'),
                    'type' =>Controls_Manager::CHOOSE,
                    'options' =>[
                        'img' =>[
                            'title' =>__('Image','htmega-addons'),
                            'icon' =>'fa fa-picture-o',
                        ],
                        'icon' =>[
                            'title' =>__('Icon','htmega-addons'),
                            'icon' =>'fa fa-info',
                        ]
                    ],
                    'default' =>'icon',
                    'condition' =>[
                        'show_light_box' =>'yes',
                    ],
                ]
            );

            $this->add_control(
                'zoom_image',
                [
                    'label' => __('Zoom Image Icon','htmega-addons'),
                    'type'=>Controls_Manager::MEDIA,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'show_light_box' =>'yes',
                        'zoomicon_type' => 'img',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => 'zoom_imagesize',
                    'default' => 'large',
                    'separator' => 'none',
                    'condition' => [
                        'show_light_box' =>'yes',
                        'zoomicon_type' => 'img',
                    ],
                    'fields_options'=>[
                        'size'=>[
                            'label' => __('Zoom Icon Size','htmega-addons')
                        ]
                    ]
                ]
            );

            $this->add_control(
                'zoom_icon',
                [
                    'label' =>__('Zoom Icon','htmega-addons'),
                    'type'=>Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'fas fa-plus',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'show_light_box' =>'yes',
                        'zoomicon_type' => 'icon',
                    ]
                ]
            );

            $this->add_control(
                'comment_icon',
                [
                    'label' =>__('Comment Icon','htmega-addons'),
                    'type'=>Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'far fa-comment',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'show_comment' =>'yes',
                    ]
                ]
            );

            $this->add_control(
                'like_icon',
                [
                    'label' =>__('Like Icon','htmega-addons'),
                    'type'=>Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'far fa-heart',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'show_like' =>'yes',
                    ]
                ]
            );

            $this->add_control(
                'flow_button_icon',
                [
                    'label' =>__('Flow Button Icon','htmega-addons'),
                    'type'=>Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'fab fa-instagram',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'show_flow_button' =>'yes',
                    ]
                ]
            );

            $this->add_control(
                'flow_button_txt',
                [
                    'label' => __( 'Flow Button Prefix', 'htmega-addons' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => __( 'Follow @', 'htmega-addons' ),
                ]
            );

        $this->end_controls_section();

        // Slider setting
        $this->start_controls_section(
            'instagram_slider_option',
            [
                'label' => esc_html__( 'Slider Option', 'htmega-addons' ),
                'condition' => [
                    'slider_on' => 'yes',
                ]
            ]
        );

            $this->add_control(
                'slitems',
                [
                    'label' => esc_html__( 'Slider Items', 'htmega-addons' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 20,
                    'step' => 1,
                    'default' => 8,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slarrows',
                [
                    'label' => esc_html__( 'Slider Arrow', 'htmega-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slprevicon',
                [
                    'label' => __( 'Previous icon', 'htmega-addons' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'fas fa-angle-left',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'slider_on' => 'yes',
                        'slarrows' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slnexticon',
                [
                    'label' => __( 'Next icon', 'htmega-addons' ),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value'=>'fas fa-angle-right',
                        'library' => 'solid',
                    ],
                    'condition' => [
                        'slider_on' => 'yes',
                        'slarrows' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'sldots',
                [
                    'label' => esc_html__( 'Slider dots', 'htmega-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slpause_on_hover',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => __('No', 'htmega-addons'),
                    'label_on' => __('Yes', 'htmega-addons'),
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'label' => __('Pause on Hover?', 'htmega-addons'),
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slcentermode',
                [
                    'label' => esc_html__( 'Center Mode', 'htmega-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'no',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slcenterpadding',
                [
                    'label' => esc_html__( 'Center padding', 'htmega-addons' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                    'default' => 50,
                    'condition' => [
                        'slider_on' => 'yes',
                        'slcentermode' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slautolay',
                [
                    'label' => esc_html__( 'Slider auto play', 'htmega-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'separator' => 'before',
                    'default' => 'no',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slautoplay_speed',
                [
                    'label' => __('Autoplay speed', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3000,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slanimation_speed',
                [
                    'label' => __('Autoplay animation speed', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 300,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slscroll_columns',
                [
                    'label' => __('Slider item to scroll', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 10,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'heading_tablet',
                [
                    'label' => __( 'Tablet', 'htmega-addons' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'after',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'sltablet_display_columns',
                [
                    'label' => __('Slider Items', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 8,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'sltablet_scroll_columns',
                [
                    'label' => __('Slider item to scroll', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 8,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'sltablet_width',
                [
                    'label' => __('Tablet Resolution', 'htmega-addons'),
                    'description' => __('The resolution to tablet.', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 750,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'heading_mobile',
                [
                    'label' => __( 'Mobile Phone', 'htmega-addons' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'after',
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slmobile_display_columns',
                [
                    'label' => __('Slider Items', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 4,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slmobile_scroll_columns',
                [
                    'label' => __('Slider item to scroll', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 1,
                    'max' => 4,
                    'step' => 1,
                    'default' => 1,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'slmobile_width',
                [
                    'label' => __('Mobile Resolution', 'htmega-addons'),
                    'description' => __('The resolution to mobile.', 'htmega-addons'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 480,
                    'condition' => [
                        'slider_on' => 'yes',
                    ]
                ]
            );

        $this->end_controls_section(); // Slider Option end

        // Style tab section
        $this->start_controls_section(
            'htmega_instagram_style_section',
            [
                'label' => __( 'Style', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'instagram_background',
                    'label' => __( 'Background', 'htmega-addons' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list',
                ]
            );

            $this->add_responsive_control(
                'instagram_margin',
                [
                    'label' => __( 'Margin', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'instagram_padding',
                [
                    'label' => __( 'Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section(); // Style Section

        // Item Style
        $this->start_controls_section(
            'htmega_instagram_item_style_section',
            [
                'label' => __( 'Item', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'instagram_item_background',
                    'label' => __( 'Background', 'htmega-addons' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li',
                ]
            );

            $this->add_responsive_control(
                'instagram_item_margin',
                [
                    'label' => __( 'Margin', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' =>'before',
                ]
            );

            $this->add_responsive_control(
                'instagram_item_padding',
                [
                    'label' => __( 'Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'instagram_item_border',
                    'label' => __( 'Border', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li',
                ]
            );

            $this->add_responsive_control(
                'instagram_item_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );

            $this->add_control(
                'instagram_item_overlay_color',
                [
                    'label' => __( 'Overlay Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => 'rgba(0, 0, 0, 0.7)',
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip::before' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section(); // Item Style end

        // Zoom icon Style
        $this->start_controls_section(
            'htmega_instagram_icon_style_section',
            [
                'label' => __( 'Zoom Icon', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'zoomicon_type'=>'icon',
                    'zoom_icon[value]!'=>'',
                ]
            ]
        );

            $this->add_control(
                'icon_size',
                [
                    'label' => __( 'Font Size', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 43,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon svg' => 'font-size: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'instagram_icon_color',
                [
                    'label' => __( 'Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon i' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon svg' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'instagram_icon_background',
                    'label' => __( 'Background', 'htmega-addons' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon',
                ]
            );

            $this->add_responsive_control(
                'instagram_icon_padding',
                [
                    'label' => __( 'Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'instagram_icon_border',
                    'label' => __( 'Border', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon',
                ]
            );

            $this->add_responsive_control(
                'instagram_icon_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list .zoom_icon' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );

        $this->end_controls_section(); // Zoom icon Style end

        // Zoom icon Style
        $this->start_controls_section(
            'htmega_instagram_commentlike_style_section',
            [
                'label' => __( 'Comment & Like', 'htmega-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_control(
                'commentlike_size',
                [
                    'label' => __( 'Font Size', 'htmega-addons' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 16,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span' => 'font-size: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'instagram_commentlike_color',
                [
                    'label' => __( 'Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'instagram_commentlike_padding',
                [
                    'label' => __( 'Padding', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'instagram_commentlike_margin',
                [
                    'label' => __( 'Margin', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'instagram_commentlike_border',
                    'label' => __( 'Border', 'htmega-addons' ),
                    'selector' => '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span',
                ]
            );

            $this->add_responsive_control(
                'instagram_commentlike_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .htmega-instragram ul.htmega-instagram-list li .instagram-clip .htmega-content .instagram-like-comment span' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    ],
                ]
            );
        
        $this->end_controls_section(); // Zoom icon Style end

        // Style Follow Button style start
        $this->start_controls_section(
            'htmega_instagram_follow_btn_style',
            [
                'label'     => __( 'Follow Button', 'htmega-addons' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'show_flow_button'  => 'yes',
                ],
            ]
        );
            
            $this->add_control(
                'follow_btn_margin',
                [
                    'label' => __( 'Margin', 'htmega-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} a.instagram_follow_btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'follow_btn_color',
                [
                    'label' => __( 'Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a.instagram_follow_btn' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'follow_btn_bg_color',
                [
                    'label' => __( 'Background Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a.instagram_follow_btn' => 'background-color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_control(
                'follow_btn_icon_bg_color',
                [
                    'label' => __( 'Icon Background Color', 'htmega-addons' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a.instagram_follow_btn i' => 'background-color: {{VALUE}}',
                    ],
                ]
            );

        $this->end_controls_section(); // Follow Button Style end



        // Style instagram arrow style start
        $this->start_controls_section(
            'htmega_instagram_arrow_style',
            [
                'label'     => __( 'Arrow', 'htmega-addons' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'slider_on' => 'yes',
                    'slarrows'  => 'yes',
                ],
            ]
        );
            
            $this->start_controls_tabs( 'instagram_arrow_style_tabs' );

                // Normal tab Start
                $this->start_controls_tab(
                    'instagram_arrow_style_normal_tab',
                    [
                        'label' => __( 'Normal', 'htmega-addons' ),
                    ]
                );

                    $this->add_control(
                        'htmega_instagram_arrow_color',
                        [
                            'label' => __( 'Color', 'htmega-addons' ),
                            'type' => Controls_Manager::COLOR,
                            'default' => '#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'htmega_instagram_arrow_fontsize',
                        [
                            'label' => __( 'Font Size', 'htmega-addons' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 100,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 20,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'instagram_arrow_background',
                            'label' => __( 'Background', 'htmega-addons' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-arrow',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'htmega_instagram_arrow_border',
                            'label' => __( 'Border', 'htmega-addons' ),
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-arrow',
                        ]
                    );

                    $this->add_responsive_control(
                        'htmega_instagram_arrow_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                    $this->add_control(
                        'htmega_instagram_arrow_height',
                        [
                            'label' => __( 'Height', 'htmega-addons' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 1000,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 30,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'htmega_instagram_arrow_width',
                        [
                            'label' => __( 'Width', 'htmega-addons' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 1000,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 30,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'width: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_responsive_control(
                        'htmega_instagram_arrow_padding',
                        [
                            'label' => __( 'Padding', 'htmega-addons' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => [ 'px', '%', 'em' ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'separator' =>'before',
                        ]
                    );

                $this->end_controls_tab(); // Normal tab end

                // Hover tab Start
                $this->start_controls_tab(
                    'instagram_arrow_style_hover_tab',
                    [
                        'label' => __( 'Hover', 'htmega-addons' ),
                    ]
                );

                    $this->add_control(
                        'htmega_instagram_arrow_hover_color',
                        [
                            'label' => __( 'Color', 'htmega-addons' ),
                            'type' => Controls_Manager::COLOR,
                            'default' => '#ffffff',
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow:hover' => 'color: {{VALUE}};',
                            ],
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'instagram_arrow_hover_background',
                            'label' => __( 'Background', 'htmega-addons' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-arrow:hover',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'htmega_instagram_arrow_hover_border',
                            'label' => __( 'Border', 'htmega-addons' ),
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-arrow:hover',
                        ]
                    );

                    $this->add_responsive_control(
                        'htmega_instagram_arrow_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-arrow:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Hover tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // Style instagram arrow style end


        // Style instagram Dots style start
        $this->start_controls_section(
            'htmega_instagram_dots_style',
            [
                'label'     => __( 'Pagination', 'htmega-addons' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' =>[
                    'slider_on' => 'yes',
                    'sldots'  => 'yes',
                ],
            ]
        );
            
            $this->start_controls_tabs( 'instagram_dots_style_tabs' );

                // Normal tab Start
                $this->start_controls_tab(
                    'instagram_dots_style_normal_tab',
                    [
                        'label' => __( 'Normal', 'htmega-addons' ),
                    ]
                );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'instagram_dots_background',
                            'label' => __( 'Background', 'htmega-addons' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-dots li',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'htmega_instagram_dots_border',
                            'label' => __( 'Border', 'htmega-addons' ),
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-dots li',
                        ]
                    );

                    $this->add_responsive_control(
                        'htmega_instagram_dots_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-dots li' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                    $this->add_control(
                        'htmega_instagram_dots_height',
                        [
                            'label' => __( 'Height', 'htmega-addons' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 1000,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-dots li' => 'height: {{SIZE}}{{UNIT}};',
                            ],
                        ]
                    );

                    $this->add_control(
                        'htmega_instagram_dots_width',
                        [
                            'label' => __( 'Width', 'htmega-addons' ),
                            'type' => Controls_Manager::SLIDER,
                            'size_units' => [ 'px', '%' ],
                            'range' => [
                                'px' => [
                                    'min' => 0,
                                    'max' => 1000,
                                    'step' => 1,
                                ],
                                '%' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                            'default' => [
                                'unit' => 'px',
                                'size' => 15,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-dots li' => 'width: {{SIZE}}{{UNIT}} !important;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Normal tab end

                // Hover tab Start
                $this->start_controls_tab(
                    'instagram_dots_style_hover_tab',
                    [
                        'label' => __( 'Active', 'htmega-addons' ),
                    ]
                );

                    $this->add_group_control(
                        Group_Control_Background::get_type(),
                        [
                            'name' => 'instagram_dots_hover_background',
                            'label' => __( 'Background', 'htmega-addons' ),
                            'types' => [ 'classic', 'gradient' ],
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-dots li.slick-active',
                        ]
                    );

                    $this->add_group_control(
                        Group_Control_Border::get_type(),
                        [
                            'name' => 'htmega_instagram_dots_hover_border',
                            'label' => __( 'Border', 'htmega-addons' ),
                            'selector' => '{{WRAPPER}} .htmega-instragram .slick-dots li.slick-active',
                        ]
                    );

                    $this->add_responsive_control(
                        'htmega_instagram_dots_hover_border_radius',
                        [
                            'label' => esc_html__( 'Border Radius', 'htmega-addons' ),
                            'type' => Controls_Manager::DIMENSIONS,
                            'selectors' => [
                                '{{WRAPPER}} .htmega-instragram .slick-dots li.slick-active' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                            ],
                        ]
                    );

                $this->end_controls_tab(); // Hover tab end

            $this->end_controls_tabs();

        $this->end_controls_section(); // Style instagram dots style end

    }

    protected function render( $instance = [] ) {

        $settings   = $this->get_settings_for_display();
        $id         = $this->get_id();

        $this->add_render_attribute( 'htmega_instragram', 'class', 'htmega-instragram' );
        $this->add_render_attribute( 'htmega_instragram', 'class', 'htmega-instragram-style-'.$settings['instagram_style'] );
        $imagesize = (int)$settings['instagram_image_size'];

        $limit      = !empty( $settings['limit'] ) ? $settings['limit'] : 8;
        $username   = !empty( $settings['username'] ) ? $settings['username'] : 'portfolio.devitems';
        $profile_link  = 'https://www.instagram.com/'.$username;

        
        if( $settings['slider_on'] == 'yes' ){
            
            $slider_settings = [
                'arrows' => ('yes' === $settings['slarrows']),
                'arrow_prev_txt' => HTMega_Icon_manager::render_icon( $settings['slprevicon'], [ 'aria-hidden' => 'true' ] ),
                'arrow_next_txt' => HTMega_Icon_manager::render_icon( $settings['slnexticon'], [ 'aria-hidden' => 'true' ] ),
                'dots' => ('yes' === $settings['sldots']),
                'autoplay' => ('yes' === $settings['slautolay']),
                'autoplay_speed' => absint($settings['slautoplay_speed']),
                'animation_speed' => absint($settings['slanimation_speed']),
                'pause_on_hover' => ('yes' === $settings['slpause_on_hover']),
                'center_mode' => ( 'yes' === $settings['slcentermode']),
                'center_padding' => absint($settings['slcenterpadding']),
            ];

            $slider_responsive_settings = [
                'display_columns' => $settings['slitems'],
                'scroll_columns' => $settings['slscroll_columns'],
                'tablet_width' => $settings['sltablet_width'],
                'tablet_display_columns' => $settings['sltablet_display_columns'],
                'tablet_scroll_columns' => $settings['sltablet_scroll_columns'],
                'mobile_width' => $settings['slmobile_width'],
                'mobile_display_columns' => $settings['slmobile_display_columns'],
                'mobile_scroll_columns' => $settings['slmobile_scroll_columns'],

            ];

            $slider_settings = array_merge( $slider_settings, $slider_responsive_settings );
        }else{
            $slider_settings = [];
        }
       
        ?>
            <div <?php echo $this->get_render_attribute_string('htmega_instragram'); ?> >

                <div id="htmega-instagram-list-<?php echo $id; ?>"></div>

                <?php 
                    if( $settings['show_flow_button'] == 'yes' ): 
                        $flowtxt = $settings['flow_button_txt'].' '.$username;
                ?>
                    <a class="instagram_follow_btn" href="<?php echo esc_url( $profile_link ); ?>" target="_blank">
                        <?php echo HTMega_Icon_manager::render_icon( $settings['flow_button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                        <span><?php echo esc_html__( $flowtxt, 'htmega-addons' );?></span>
                    </a>
                <?php endif; ?>

            </div>

            <?php
                $zoo_image = '';
                if( !empty( $settings['zoom_image'] ) && $settings['zoomicon_type'] == 'img' ){
                    $zoo_image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'zoom_imagesize', 'zoom_image' );
                }else{
                    $zoo_image = sprintf('<span class="zoom_icon">%1$s</span>', HTMega_Icon_manager::render_icon( $settings['zoom_icon'], [ 'aria-hidden' => 'true' ] ) );
                }
            ?>

            <script type="text/javascript">
                ;jQuery(document).ready(function($) {
                'use strict';

                    var limit       = <?php echo $limit; ?>,
                        id          = '<?php echo $id; ?>',
                        username    = '<?php echo $username; ?>',
                        img_size    = <?php echo $imagesize; ?>,
                        slider_on   = '<?php echo $settings['slider_on']; ?>';

                    // Meta Option
                    var comment  = '<?php echo $settings['show_comment']; ?>',
                        like     = '<?php echo $settings['show_like']; ?>',
                        lightbox = '<?php echo $settings['show_light_box']; ?>',
                        like_icon = '<?php echo HTMega_Icon_manager::render_icon( $settings['like_icon'], [ 'aria-hidden' => 'true' ] ); ?>',
                        comment_icon = '<?php echo HTMega_Icon_manager::render_icon( $settings['comment_icon'], [ 'aria-hidden' => 'true' ] ); ?>',
                        zoo_image = '<?php echo $zoo_image; ?>',
                        lazy_load = '<?php echo $settings['lazy_load']; ?>';

                    // Slider Option
                    if( slider_on == 'yes' ){
                        var slider_opt = <?php echo wp_json_encode( $slider_settings ); ?>;
                        var arrows = slider_opt['arrows'],
                            arrow_prev_txt = slider_opt['arrow_prev_txt'],
                            arrow_next_txt = slider_opt['arrow_next_txt'],
                            dots = slider_opt['dots'],
                            autoplay = slider_opt['autoplay'],
                            autoplay_speed = parseInt(slider_opt['autoplay_speed']) || 3000,
                            animation_speed = parseInt(slider_opt['animation_speed']) || 300,
                            pause_on_hover = slider_opt['pause_on_hover'],
                            center_mode = slider_opt['center_mode'],
                            center_padding = slider_opt['center_padding'] ? slider_opt['center_padding'] : '50px',
                            display_columns = parseInt(slider_opt['display_columns']) || 1,
                            scroll_columns = parseInt(slider_opt['scroll_columns']) || 1,
                            tablet_width = parseInt(slider_opt['tablet_width']) || 800,
                            tablet_display_columns = parseInt(slider_opt['tablet_display_columns']) || 1,
                            tablet_scroll_columns = parseInt(slider_opt['tablet_scroll_columns']) || 1,
                            mobile_width = parseInt(slider_opt['mobile_width']) || 480,
                            mobile_display_columns = parseInt(slider_opt['mobile_display_columns']) || 1,
                            mobile_scroll_columns = parseInt(slider_opt['mobile_scroll_columns']) || 1;
                    }

                    // Manage Image Size
                    var image_sizes = {
                        "150": 0,
                        "240": 1,
                        "320": 2,
                        "480": 3,
                        "640": 4
                    };

                    //image size
                    var image_index = typeof image_sizes[img_size] !== "undefined" ? image_sizes[img_size] : image_sizes[640];

                    // Clear localStorage data
                    var clearLocalData = '<?php echo $settings['clear_cache_data']; ?>';
                    if( clearLocalData == 'yes' ){
                        window.localStorage.clear();
                    }

                    // Display Instagram item
                    function htMegaDisplayInstagramFeed( data ){
                        var html = "<ul class='htmega-instagram-list'>";
                        var imgs = (data.edge_owner_to_timeline_media || data.edge_hashtag_to_media).edges,
                        max = ( imgs.length > limit ) ? limit : imgs.length;

                        for (var i = 0; i < max; i++) {
                            var url = "https://www.instagram.com/p/" + imgs[i].node.shortcode,
                                image, fullimage, type_resource, caption;

                            switch (imgs[i].node.__typename) {
                                case "GraphSidecar":
                                    type_resource = "sidecar"
                                    image = imgs[i].node.thumbnail_resources[image_index].src;
                                    fullimage = imgs[i].node.thumbnail_src;
                                    break;
                                case "GraphVideo":
                                    type_resource = "video";
                                    image = imgs[i].node.thumbnail_src
                                    fullimage = imgs[i].node.thumbnail_src;
                                    break;
                                default:
                                    type_resource = "image";
                                    image = imgs[i].node.thumbnail_resources[image_index].src;
                                    fullimage = imgs[i].node.thumbnail_src;
                            }

                            if (
                                typeof imgs[i].node.edge_media_to_caption.edges[0] !== "undefined" &&
                                typeof imgs[i].node.edge_media_to_caption.edges[0].node !== "undefined" &&
                                typeof imgs[i].node.edge_media_to_caption.edges[0].node.text !== "undefined" &&
                                imgs[i].node.edge_media_to_caption.edges[0].node.text !== null
                            ) {
                                caption = imgs[i].node.edge_media_to_caption.edges[0].node.text;
                            } else if (
                                typeof imgs[i].node.accessibility_caption !== "undefined" &&
                                imgs[i].node.accessibility_caption !== null
                            ) {
                                caption = imgs[i].node.accessibility_caption;
                            } else {
                                caption = (is_tag ? data.name : data.username) + " image " + i;
                            }

                            html += '<li>';
                                html += "<a href='" + url + "' rel='noopener' target='_blank'>";
                                html += "<img" + (lazy_load == 'yes' ? " loading='lazy'" : '')  +" src='" + image + "' alt='" + caption + "' />";
                                html += "</a>";

                                if( comment == 'yes' || like == 'yes' || lightbox == 'yes' ){

                                    html += '<div class="instagram-clip"><div class="htmega-content">';

                                        if( comment == 'yes' || like == 'yes' ){
                                            html += '<div class="instagram-like-comment">';
                                                if( like == 'yes' ){
                                                    html += '<span class="like">'+like_icon+imgs[i].node.edge_liked_by.count+'</span>';
                                                }
                                                if( comment == 'yes' ){
                                                    html += '<span class="comment">'+comment_icon+imgs[i].node.edge_media_to_comment.count+'</span>';
                                                }
                                            html +='</div>';
                                        }

                                        if( lightbox == 'yes' ){
                                            html += '<div class="instagram-btn">';
                                                html += '<a class="image-popup-vertical-fit" href="'+ fullimage +'">'+zoo_image+'</a>';
                                            html += '</div>';
                                        }

                                    html += '</div></div>';
                                }


                            html += '</li>';
                        }

                        html += '</ul>';
                        
                        $( "#htmega-instagram-list-"+id ).html( html );
                    }
                    
                    // Instagram Feed
                    if( window.localStorage.getItem( "htmega_instragram_local_data_status"+id ) === 'true' ){
                        var localdata = window.localStorage.getItem( "htmega_instragram_local_data_"+id );
                        htMegaDisplayInstagramFeed( JSON.parse( localdata ) );
                    }else{
                        $.instagramFeed({
                            'username': username,
                            'callback': function( data ){
                                window.localStorage.setItem( "htmega_instragram_local_data_"+id, JSON.stringify( data ) );
                                window.localStorage.setItem( "htmega_instragram_local_data_status"+id, 'true' );
                                htMegaDisplayInstagramFeed( data );
                            }
                        });
                    }

                    if( slider_on == 'yes' ){

                        function htMegaInstagramSlider( selector = 'htmega-instagram-list' ){
                            $("#htmega-instagram-list-"+id+" ." + selector ).slick({
                                arrows: arrows,
                                prevArrow: '<button class="htmega-carosul-prev">'+arrow_prev_txt+'</button>',
                                nextArrow: '<button class="htmega-carosul-next">'+arrow_next_txt+'</button>',
                                dots: dots,
                                infinite: true,
                                autoplay: autoplay,
                                autoplaySpeed: autoplay_speed,
                                speed: animation_speed,
                                fade: false,
                                pauseOnHover: pause_on_hover,
                                slidesToShow: display_columns,
                                slidesToScroll: scroll_columns,
                                centerMode: center_mode,
                                centerPadding: center_padding,
                                responsive: [
                                    {
                                        breakpoint: tablet_width,
                                        settings: {
                                            slidesToShow: tablet_display_columns,
                                            slidesToScroll: tablet_scroll_columns
                                        }
                                    },
                                    {
                                        breakpoint: mobile_width,
                                        settings: {
                                            slidesToShow: mobile_display_columns,
                                            slidesToScroll: mobile_scroll_columns
                                        }
                                    }
                                ]
                            })
                        }

                        if( window.localStorage.getItem( "htmega_instragram_local_data_status"+id ) === 'true' ){
                            htMegaInstagramSlider();
                        }else{
                            $("#htmega-instagram-list-"+id).on("DOMNodeInserted", function (e) {
                                if ( e.target.className == 'htmega-instagram-list' ) {
                                    htMegaInstagramSlider();
                                }
                            });
                        }
                        
                    }

                });
            </script>

        <?php
    }

}