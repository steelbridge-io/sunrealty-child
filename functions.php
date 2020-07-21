<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function load_scripts_wpresidence_child() {

  wp_register_style( 'load_child_theme_style', get_stylesheet_directory_uri() . '/css/custom.css' );
  
  wp_enqueue_style( 'load_child_theme_style' );
  
  
  if( is_page('search-real-estate') || is_page('mobile-homes-with-land')) {
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(), '1.0', true );
  }
  
}
add_action('wp_enqueue_scripts', 'load_scripts_wpresidence_child');

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function admin_scripts_styles() {
  wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/css/admin.css', false, '1.0.0' );
  wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'admin_scripts_styles' );

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

/*
if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css',array('wpestate_bootstrap','wpestate_bootstrap_theme') );
    }
endif;

load_child_theme_textdomain('wpresidence', get_stylesheet_directory().'/languages');
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css' ); */

// END ENQUEUE PARENT ACTION


if ( !function_exists( 'wpestate_chld_thm_cfg_parent_css' ) ):
    function wpestate_chld_thm_cfg_parent_css() {
        $parent_style = 'wpestate_style';
        wp_enqueue_style('bootstrap.min',get_theme_file_uri('/css/bootstrap.min.css'), array(), '1.0', 'all');
        wp_enqueue_style('bootstrap-theme.min',get_theme_file_uri('/css/bootstrap-theme.min.css'), array(), '1.0', 'all');

        $use_mimify     =   wpresidence_get_option('wp_estate_use_mimify','');
        $mimify_prefix  =   '';
        if($use_mimify==='yes'){
            $mimify_prefix  =   '.min';
        }

        if($mimify_prefix===''){
            wp_enqueue_style($parent_style,get_template_directory_uri().'/style.css', array('bootstrap.min','bootstrap-theme.min'), '1.0', 'all');
        }else{
            wp_enqueue_style($parent_style,get_template_directory_uri().'/style.min.css', array('bootstrap.min','bootstrap-theme.min'), '1.0', 'all');
        }

        if ( is_rtl() ) {
            wp_enqueue_style( 'chld_thm_cfg_parent-rtl',  trailingslashit( get_template_directory_uri() ). '/rtl.css' );
        }
        wp_enqueue_style( 'wpestate-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            array( $parent_style ),
            wp_get_theme()->get('Version')
        );

    }
endif;

load_child_theme_textdomain('wpresidence', get_stylesheet_directory().'/languages');
add_action( 'wp_enqueue_scripts', 'wpestate_chld_thm_cfg_parent_css' );

/*****
ALL CUSTOM FUNCTIONS START BELOW THIS T_LINE
*****/

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );



/*  -----------

AWS had a problem working with this theme wpresidence. In the agent login, images were uploaded, but the path was not updated to the AWS path, the path to the bucket (ex:https://s3.amazonaws.com/sunrealty-florida-media/wp-content/uploads/2018/09/12051230/sept-141-120x120.jpg). The function fileupload_process below addresses that problem by using a filer 'as3cf_filter_post_local_to_s3'.

---------*/

function fileupload_process($file){

    
    
    if( $file['type']!='application/pdf'    ){
        if( intval($file['height'])<500 || intval($file['width']) <500 ){
            $response = array('success' => false,'image'=>true);
            print json_encode($response);
            exit;
        }
    }
    
  
    
    $attachment = handle_file($file);

    if (is_array($attachment)) {
        $html = getHTML($attachment);
        
        // Added per wpresidence update for AmazonS3 user photo urls not converting
        $html = $content = apply_filters( 'as3cf_filter_post_local_to_s3', $html );

        $response = array(
            'base' =>  $file['base'],
            'type'      =>  $file['type'],
            'height'      =>  $file['height'],
            'width'      =>  $file['width'],
            'success'   => true,
            'html'      => $html,
            'attach'    => $attachment['id'],


        );

        print json_encode($response);
        exit;
    }

    $response = array('success' => false);
    print json_encode($response);
    exit;
    }


    // Change noreply email name
    // Function to change email address

    function wpb_sender_email( $original_email_address ) {
        return 'noreply@sunrealty-florida.com';
    }

    // Function to change sender name
    function wpb_sender_name( $original_email_from ) {
        return 'Sun Realty Florida';
    }

    // Hooking up our functions to WordPress filters
    add_filter( 'wp_mail_from', 'wpb_sender_email' );
    add_filter( 'wp_mail_from_name', 'wpb_sender_name' );

/*add_filter( 'upload_size_limit', 'PBP_increase_upload' );
function PBP_increase_upload( $bytes )
{
    return 4048576; // 1 megabyte
}*/

function date_shortcode() {
    return '<p class="srf-crdentials footer-copy">&copy;Sun Realty Florida 2005 - ' . date('Y') . '</p>';
}
add_shortcode('adddate', 'date_shortcode');

// WP Kraken #x821271: Modify Agent Listings AJAX function
if( !function_exists('wpestate_agent_listings') ) {
    function wpestate_agent_listings(){
        check_ajax_referer( 'wpestate_agent_listings_nonce', 'security' );
        global $wpestate_options;
        global $wpestate_no_listins_per_row;
        global $wpestate_custom_unit_structure;
        global $show_remove_fav;
        global $prop_unit_class;
        global $wpestate_prop_unit;
        global $wpestate_property_unit_slider;
        global $custom_post_type;
        global $col_class;
        global $wpestate_custom_unit_structure;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_included_ids;
        global $wpestate_currency;
        global $where_currency;

        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $term_name=esc_html($_POST['term_name']);
        $agent_id = esc_html($_POST['agent_id']);
        $post_id = esc_html($_POST['post_id']);


        $show_compare               =   1;
        $align_class                =   '';
        $wpestate_prop_unit                  =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
        $prop_unit_class            =   '';
        if($wpestate_prop_unit=='list'){
            $prop_unit_class="ajax12";
            $align_class=   'the_list_view';
        }

        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $wpestate_uset_unit         =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $wpestate_no_listins_per_row         =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');
        $taxonmy                    =   get_query_var('taxonomy');
        $term                       =   get_query_var( 'term' );
        $wpestate_property_unit_slider       =   wpresidence_get_option('wp_estate_prop_list_slider','');
        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }

        if( is_tax() && $custom_post_type=='estate_agent'){
        global $wpestate_no_listins_per_row;
        $wpestate_no_listins_per_row       =   intval( wpresidence_get_option('wp_estate_agent_listings_per_row', '') );

        $col_class=4;
        if($wpestate_options['content_class']=='col-md-12'){
            $col_class=3;
        }

        if($wpestate_no_listins_per_row==3){
            $col_class  =   '6';
            $col_org    =   6;
            if($wpestate_options['content_class']=='col-md-12'){
                $col_class  =   '4';
                $col_org    =   4;
            }
        }else{
            $col_class  =   '4';
            $col_org    =   4;
            if($wpestate_options['content_class']=='col-md-12'){
                $col_class  =   '3';
                $col_org    =   3;
            }
        }

        }

        $page_id        =   get_user_meta($agent_id,'user_agent_id',true);
        $wpestate_options        =   wpestate_page_details($page_id);


        $show_remove_fav=0;
        wp_suspend_cache_addition(false);
        $wpestate_uset_unit         =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $wpestate_no_listins_per_row         =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');


        $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');
        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        $prop_no    =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }

        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );






        $action_array=array(
                        'taxonomy'     => 'property_action_category', // WP Kraken #x821271: Change terms to action category
                        'field'        => 'slug',
                        'terms'        => $term_name
        );


                if( $agent_id == '-1' ){
                        $args = array(
                                'post_type'         =>  'estate_property',

                                'paged'             =>  $paged,
                                'posts_per_page'    =>  $prop_no,
                                'post_status'       => 'publish',
                                'meta_key'          => 'prop_featured',
                                'orderby'           => 'meta_value',
                                'order'             => 'DESC',
                                'tax_query'         =>  array(
                                                            'relation' => 'AND',
                                                            $action_array,
                                                            ),
                                'meta_query'        =>  array(
                                                            array(
                                                                    'key'     => 'property_agent',
                                                                    'value'   => $post_id,
                                                            ),
                                                        ),
                                );
                }else{
                        $args = array(
                                'post_type'         =>  'estate_property',
                                'author'            =>  $agent_id,
                                'paged'             =>  $paged,
                                'posts_per_page'    =>  $prop_no,
                                'post_status'       =>  'publish',
                                'meta_key'          =>  'prop_featured',
                                'orderby'           =>  'meta_value',
                                'order'             =>  'DESC',
                                'tax_query'         =>  array(
                                                            'relation' => 'AND',
                                                            $action_array,
                                                        )
                                );
                }


        if( (int)$_POST['loaded'] ){
            $args['offset'] = (int)$_POST['loaded'];
        }

        if($term_name=='all'){
            // WP Kraken #x821271: Show only for-sale and pending in All tab
            //unset($args['tax_query']);
            $args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'property_action_category',
                    'field'    => 'slug',
                    'terms'    => 'for-sale',
                ),
                array(
                    'taxonomy' => 'property_action_category',
                    'field'    => 'slug',
                    'terms'    => 'pending',
                ),
            );
        }

        $prop_selection = wpestate_return_filtered_by_order($args);

        if( $prop_selection->have_posts() ){

            while ($prop_selection->have_posts()): $prop_selection->the_post();
                include( locate_template('templates/property_unit'.$property_card_type_string.'.php') );
            endwhile;

        }else{
            print '<span class="no_results">'. esc_html__("We didn't find any results","wpresidence").'</>';
        }

        die();
    }
}
