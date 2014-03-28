<?php

// Set-up Action and Filter Hooks
add_action('admin_init', 'veuse_priceitemdocumentation_init' );
add_action('admin_menu', 'veuse_priceitemdocumentation_add_options_page');


// Init plugin options to white list our options
function veuse_priceitemdocumentation_init(){
	register_setting( 'veuse_priceitemdocumentation_plugin_options', 'veuse_priceitemdocumentation_options', 'veuse_priceitemdocumentation_validate_options' );
}


// Add menu page
function veuse_priceitemdocumentation_add_options_page() {
	add_submenu_page( 'edit.php?post_type=priceitem', __('Pricetabble documentation page'), __('Documentation'), 'edit_themes', 'priceitem_documentation', 'veuse_priceitemdocumentation_render_form');

}



function get_all_priceitem_tabs(){
	
	 $tabs = array( 
    	
    	'intro' 		=> 'Intro', 
    	'categories' 	=> 'Pricetables', 
    	'posts'			=> 'Price items',
    	'display'		=> 'Adding a pricetable to a page'
    	
    	);
    return $tabs;
}

// Render the Plugin options form
function veuse_priceitemdocs_admin_tabs( $current = 'intro' ) {

    
    $tabs = get_all_priceitem_tabs();  
     
    echo '<h3 class="nav-tab-wrapper" style="padding-left:0; border-bottom:0;">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' style='padding-top:6px; margin:0px -1px -1px 0; border:1px solid #ccc;' href='?post_type=priceitem&page=priceitem_documentation&tab=$tab'>$name</a>";

    }
    echo '</h3>';
}


function veuse_priceitemdocumentation_render_form(){
    $plugin_name = 'Veuse Pricetable';
	?>
	<style>
		#veuse-priceitemdocumentation-wrapper a { text-decoration: none;}
		#veuse-priceitemdocumentation-wrapper p {  }
		#veuse-priceitemdocumentation-wrapper ul { margin-bottom: 30px !important;}
		ul.inline-list { list-style: disc !important; list-style-position: inside;}
		ul.inline-list li { display: inline; margin-right: 10px; list-style: disc;}
		ul.inline-list li:after { content:'-'; margin-left: 10px; }
	</style>
	<div class="wrap">

		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php echo $plugin_name;?> <?php _e('documentation','veuse-priceitemdocumentation');?></h2>
		<p><?php
			
			echo sprintf( __( 'Here you find instructions on how to use the %s plugin. For more in-depth info, please check out http://veuse.com/support.', 'veuse-priceitemdocumentation' ), $plugin_name);?>
		</p>
		
		<?php
		
		$docpath = get_stylesheet_directory().'/includes/priceitemdocumentation';
		
		if ( isset ( $_GET['tab'] ) ) veuse_priceitemdocs_admin_tabs($_GET['tab']); else veuse_priceitemdocs_admin_tabs('intro'); ?>
		
		<div id="veuse-priceitemdocumentation-wrapper" style="padding:20px 0; max-width:800px;">	

		<?php
		
		if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; else $tab = 'intro';
		

			
			switch ($tab ) {	
				
				
				case $tab :
				
					echo '<div>';
					
					//$text = file_get_contents($docpath."/pages/$tab.php");		
					//echo nl2br($text);
					
					include("pages/$tab.php");
										
					echo '</div>';
					
					break;
				
			} // end switch			
			

	
		?>
		<div>
		<br>
		<hr>
		<br>
		<a href="http://veuse.com/support" class="button">Support forum</a>
		</div>
		</div>
		
	</div>
	<?php
}
?>