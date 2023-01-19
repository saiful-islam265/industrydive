<?php 
if(!function_exists('register_customize')){
    function register_customize($id_customize){
        $id_customize->add_panel( 'id_panel', array(
            'priority'       => 80,
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
            'title'          => esc_html__('Theme Settings', 'industrydive'),
            'description'    => esc_html__('Custom settings for theme', 'industrydive'),
        ) );

        $id_customize->add_setting( 'featured_post' , array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'tiles',
            'transport'   => 'refresh',
        ) );

        $id_customize->add_section( 'home_section' , array(
            'title'      => esc_html__( 'Home', 'industrydive' ),
            'panel'     => 'id_panel'
        ) );

        $id_customize->add_control( 'featured_post', array(
            'type' => 'select',
            'section' => 'home_section',
            'label' => __( 'Show featured posts as', 'industrydive' ),
            'choices' => array(
              'tiles' => __( 'Tiles', 'industrydive' ),
              'slider' => __( 'Slider', 'industrydive' )
            ),
        ) );
    }
    add_action('customize_register', 'register_customize');
}