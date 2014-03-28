<?php
/*
Plugin Name: Veuse Pricetable
Plugin URI: http://veuse.com/veuse-priceitem
Description:  Easily create stunning pricetables an insert on your pages with a shortcode or widget
Version: 1.0
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
Text Domain: veuse-pricetable
Domain Path: /languages
Tags: pricetable
*/


class VeusePricetable {

	private $strVeusePricetableURI = '';
	private $strVeusePricetablePATH = '';
	

	function __construct() {
	

		$this->strVeusePricetableURI  = plugin_dir_url(__FILE__) ;
		$this->strVeusePricetablePATH = plugin_dir_path(__FILE__) ;
				
		//add_action('init', array(&$this,'veuse_priceitem_enqueue_styles'));
	
		add_action('init', array(&$this,'register_priceitem'));
		
		add_action('plugins_loaded', array(&$this,'veuse_priceitem_action_init'));
		 
		add_filter('manage_priceitem_posts_columns',  array ( $this,'veuse_priceitem_columns'));
		add_action('manage_priceitem_posts_custom_column', array ( $this,'veuse_priceitem_custom_columns'), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array(&$this,'veuse_pricetable_admin_enqueue_scripts' ));
		
		add_action( 'wp_ajax_veuse_pricetable_update_post_order', array(&$this,'veuse_pricetable_update_post_order' ));
      
    }
    
    function veuse_pricetable_update_post_order($post_id) {
		
		global $wpdb;
	
		$post_type    = $_POST['posttype'];
		$order        = $_POST['order'];
	
		foreach( $order as $menu_order => $post_id )
		{
			$post_id        = intval( str_ireplace( 'post-', '', $post_id ) );
			echo $post_id;
			$menu_order     = intval($menu_order);
			wp_update_post( array( 'ID' => $post_id, 'menu_order' => $menu_order ) );
		}
	
		die( '1' );
	}

	
	/* Enqueue scripts
	============================================= */
	
	function veuse_priceitem_enqueue_styles() {

		$plugin_options = get_option('veuse_priceitem_options');
		
		if(is_admin()){
			
			wp_register_style( 'priceitem-meta',  $this->strVeusePricetableURI . 'assets/css/priceitem-meta.css', array(), '', 'screen' );
			wp_enqueue_style ( 'priceitem-meta' );
			
		}

			wp_register_style( 'veuse-priceitem',  $this->strVeusePricetableURI . 'assets/css/veuse-priceitem.css', array(), '', 'screen' );
			wp_enqueue_style ( 'veuse-priceitem' );

	}
	
	/* Enqueue scripts */

	function veuse_pricetable_admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'veuse-pricetable-admin-scripts', $this->strVeuseStaffURI . 'assets/js/veuse-pricetable-admin.js' );
	}
	
	/* Localization
	============================================= */
	function veuse_priceitem_action_init() {
	    load_plugin_textdomain('veuse-priceitem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	}
	
	
	/* Register post-type
	============================================= */
	
	public function register_priceitem() {

		$labels = array(
	        'name' => __( 'Pricetable', 'veuse-priceitem' ), // Tip: _x('') is used for localization
	        'singular_name' => __( 'Price Item', 'veuse-priceitem' ),
	        'add_new' => __( 'New Price Item', 'veuse-priceitem' ),
	        'add_new_item' => __( 'New Price Item','veuse-priceitem' ),
	        'edit_item' => __( 'Edit Price Item', 'veuse-priceitem' ),
	        'all_items' => __( 'All Price Items','veuse-priceitem' ),
	        'new_item' => __( 'New Price Item','veuse-priceitem' ),
	        'view_item' => __( 'View Price Item','veuse-priceitem' ),
	        'search_items' => __( 'Search Price Items','veuse-priceitem' ),
	        'not_found' =>  __( 'No Price Items','veuse-priceitem' ),
	        'not_found_in_trash' => __( 'No Price Items found in Trash','veuse-priceitem' ),
	        'parent_item_colon' => ''
	    );

		register_post_type('priceitem',
					array(
					'labels' => $labels,
					'public' => true,
					'show_ui' => true,
					'_builtin' => false, // It's a custom post type, not built in
					'_edit_link' => 'post.php?post=%d',
					'capability_type' => 'post',
					'hierarchical' => true,
					'rewrite' => array("slug" => "priceitem"), // Permalinks
					'query_var' => "priceitem", // This goes to the WP_Query schema
					'supports' => array('permalink'),
					'menu_icon' => 'dashicons-tag',
					'menu_position' => 30,
					'publicly_queryable' => false,
					'exclude_from_search' => true,
					'show_in_nav_menus' => false
					));

		$taxlabels = array(
		        'name' => __( 'Pricetable', 'veuse-priceitem' ), // Tip: _x('') is used for localization
		        'singular_label' => __( 'Pricetable', 'veuse-priceitem' ),
		        'add_new' => __( 'Add New Pricetable', 'veuse-priceitem' ),
		        'add_new_item' => __( 'Add New Pricetable','veuse-priceitem' ),
		        'edit_item' => __( 'Edit Pricetable', 'veuse-priceitem' ),
		        'all_items' => __( 'All Pricetables','veuse-priceitem' ),
		        'new_item' => __( 'New Pricetable','veuse-priceitem' ),
		        'view_item' => __( 'View Pricetable','veuse-priceitem' ),
		        'search_items' => __( 'Search Pricetables','veuse-priceitem' ),
		        'not_found' =>  __( 'No Pricetables found','veuse-priceitem' ),
		        'parent_item_colon' => ''
		    );

		register_taxonomy("pricetable",
			array("priceitem"),
			array(
				"hierarchical" => true,
				"labels" => $taxlabels,
				"rewrite" => true,
				"show_ui" => true,
				'show_in_nav_menus' => false
				)
			);
		}
		
		
		
		
		function veuse_priceitem_custom_columns($column, $post_id) {
		
			global $post;
					
			switch ($column) {	 		
			 	
			 		case 'title' :
			 	
						echo get_the_title();
						break;
				 	
				 							
					case 'description' :
				 	
					 	echo get_the_excerpt();
						break;
					
					case 'pricetable' :
				 	
					 	$taxonomy = 'pricetable';
						$post_type = get_post_type($post_id);
						$terms = get_the_terms($post_id, $taxonomy);
			
						if (!empty($terms) ) {
							foreach ( $terms as $term ){
						    	$post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
						    }
						       echo join('', $post_terms );
						}
						
						break;
					
					case 'order' :
				 	
						echo '<a class="order-staff" style="padding:12px;cursor:move; float:right;"><img src="'.plugin_dir_url(__FILE__).'assets/images/icon-move.png" width="24" height=24" alt="Move"/></a>';						
						break;
			
					
			}			
					
		}
		
		function veuse_priceitem_columns($columns){
					
			$columns = array(
					"cb" => "<input type=\"checkbox\" />",
					"title" => __("Title","veuse-priceitem"),
					"description" => __("Description","veuse-priceitem"),
					"pricetable" => __("Pricetable","veuse-priceitem"),
					"order" => __("Change order","veuse-priceitem")
					
			);
			return $columns;
		}
		
		
		

}

$pricetable = new VeusePricetable;




/* Shortcode
============================================= */

function veuse_pricetable($atts, $content = null){

   extract(shortcode_atts(array(
		'pricetable' 		=> ''
	), $atts));
	
	
	$args = array(
	    'post_type' => 'priceitem',
	    'post_status' => 'publish',
	    'orderby'	=> 	'menu_order',
	    'posts_per_page' => -1,
	    'order' => 'ASC',
	    'tax_query' => array(
	 		array(
	            'taxonomy' => 'pricetable',
	            'field' => 'slug',
	            'terms' => $pricetable
	            )
	         )
	      );

	$priceitems = get_posts( $args );
				
	$count = count($priceitems);
		
	if($count > 0){
		
		$output = '';
		$output .= '<div class="veuse-pricing-table-wrapper columns-'.$count.'">';
		
			foreach( $priceitems as $priceitem ) :	setup_postdata($priceitem);
	
				$title 		= $priceitem->post_title;
				$excerpt 	= $priceitem->post_excerpt;
				
				$featured = get_post_meta($priceitem->ID,'veuse_priceitem_featured',true);
				$ribbon_text = get_post_meta($priceitem->ID,'veuse_priceitem_ribbon_text',true);
				$ribbon_color = get_post_meta($priceitem->ID,'veuse_priceitem_ribbon_color',true);
				$price = get_post_meta($priceitem->ID,'veuse_priceitem_price',true);
				$currency = get_post_meta($priceitem->ID,'veuse_priceitem_currency',true);
				$period = get_post_meta($priceitem->ID,'veuse_priceitem_period',true);
				$bullets = get_post_meta($priceitem->ID,'veuse_priceitem_bullets',true);
				$button_href = get_post_meta($priceitem->ID,'veuse_priceitem_button_href',true);
				$button_text = get_post_meta($priceitem->ID,'veuse_priceitem_button_text',true);
				
				if($featured == 'on') { $featured = 'featured-price'; }
				
				$bullets = explode(',', $bullets);
			
				$output .= '';
				
				$output .= '<ul class="veuse-pricing-table '.$featured.'">';
				
				if(!empty($ribbon_text) && !empty($ribbon_color)){
					$output .= '<li class="ribbon ribbon-'.$ribbon_color.'"><div class="banner"><div class="text">'.$ribbon_text.'</div></div></li>';
					}
				if(!empty($title))
				$output .= '<li class="title">'.$title.'</li>';
				
				$output .= '<li class="price">';
				if(!empty($currency))
				$output .= '<span class="currency">'.$currency.'</span>';
				if(isset($price))
				$output .= '<span class="price">'.$price.'</span>';
				if(!empty($period))
				$output .= '<span class="period">'.$period.'</span>';
				$output .= '</li>';
				  
				if(!empty($excerpt))
					$output .= '<li class="description">'.$excerpt.'</li>';
				
				foreach($bullets as $bullet){
					
					$output .= '<li class="bullet-item">'.$bullet.'</li>';
					
				} 		  
			
				$output .= '<li class="cta-button"><a class="veuse-button" href="'.$button_href.'">'.$button_text.'</a></li>';
				$output .= '</ul>';
				
				
			
			endforeach;

		$output .= '</div>';
		
	}
	
			
		
	
	
	wp_reset_postdata();

	
  return $output;
}

add_shortcode('veuse_pricetable','veuse_pricetable');

/* Widget */
require_once(plugin_dir_path(__FILE__). 'widget.php');

/* Post meta */
require_once(plugin_dir_path(__FILE__). 'post-meta.php');

/* Documentation */
require_once(plugin_dir_path(__FILE__). '/documentation/documentation.php');



/* Filter the content to insert post meta-data */

if(!function_exists('veuse_pricetable_content')){

	function veuse_pricetable_content($content) {

		global $post;

		if( is_singular( 'pricetable') /* && is_main_query()*/ ) {

			/* Get meta into variables */
			$veuse_portfolio_options = get_option('veuse_portfolio_options');

			$image = get_the_post_thumbnail($post->ID, 'large');

			$categories = get_the_term_list($post->ID, 'portfolio-category','',', ','');
			$clients = get_the_term_list($post->ID, 'portfolio-client','',', ','');
			$link = get_post_meta($post->ID,'veuse_portfolio_website',true);

			$post = get_post($post->ID);

			$before_content  = '';
			$content = '';
			$after_content  = '';

			//$before_content .= '<p class="lead">'. $post->post_excerpt .'</p>';
			$content .= wpautop(do_shortcode($post->post_content)) ;


			$after_content .= '<ul class="portfolio-meta">';
				if($categories)	$after_content .= '<li><i class="icon-briefcase"></i> ' . $categories . '</li>';
	 			//if($clients)	$after_content .= '<li><i class="icon-bookmark"></i> '. $clients . '</li>';
	 			if($link)		$after_content .= '<li><i class="icon-external-link"></i> <a href="'.$link.'" rel="external">'. $link .'</a></li>';
			$after_content .= '</ul>';

			$content = $before_content . $content . $after_content;
			return $content;
		}

		else {

			return $content;
		}
	}

	add_filter('the_content', 'veuse_pricetable_content', 1);
}


?>