<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//////////// CONTRACT POST TYPE /////////////////
// Register Custom Post Type Contract
function create_contract_cpt() {

	$labels = array(
		'name' => _x( 'Contracts', 'Post Type General Name', 'tcct' ),
		'singular_name' => _x( 'Contract', 'Post Type Singular Name', 'tcct' ),
		'menu_name' => _x( 'Contracts', 'Admin Menu text', 'tcct' ),
		'name_admin_bar' => _x( 'Contract', 'Add New on Toolbar', 'tcct' ),
		'archives' => __( 'Contract Archives', 'tcct' ),
		'attributes' => __( 'Contract Attributes', 'tcct' ),
		'parent_item_colon' => __( 'Parent Contract:', 'tcct' ),
		'all_items' => __( 'All Contracts', 'tcct' ),
		'add_new_item' => __( 'Add New Contract', 'tcct' ),
		'add_new' => __( 'Add New', 'tcct' ),
		'new_item' => __( 'New Contract', 'tcct' ),
		'edit_item' => __( 'Edit Contract', 'tcct' ),
		'update_item' => __( 'Update Contract', 'tcct' ),
		'view_item' => __( 'View Contract', 'tcct' ),
		'view_items' => __( 'View Contracts', 'tcct' ),
		'search_items' => __( 'Search Contract', 'tcct' ),
		'not_found' => __( 'Not found', 'tcct' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'tcct' ),
		'featured_image' => __( 'Featured Image', 'tcct' ),
		'set_featured_image' => __( 'Set featured image', 'tcct' ),
		'remove_featured_image' => __( 'Remove featured image', 'tcct' ),
		'use_featured_image' => __( 'Use as featured image', 'tcct' ),
		'insert_into_item' => __( 'Insert into Contract', 'tcct' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Contract', 'tcct' ),
		'items_list' => __( 'Contracts list', 'tcct' ),
		'items_list_navigation' => __( 'Contracts list navigation', 'tcct' ),
		'filter_items_list' => __( 'Filter Contracts list', 'tcct' ),
	);
	$args = array(
		'label' => __( 'Contract', 'tcct' ),
		'description' => __( 'Artist and location contracts', 'tcct' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-welcome-write-blog',
		'supports' => array('title', 'editor', 'custom-fields'),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => false,
		'menu_position' => 5,
		'show_in_admin_bar' => false,
		'show_in_nav_menus' => false,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => true,
		'show_in_rest' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'contract', $args );

}

add_action( 'init', 'create_contract_cpt', 0 );

//// CUSTOM FIELDS ////

require_once ( 'Tax-meta-class/Tax-meta-class.php' );

if (is_admin()) {

  $prefix = 'tcct_';

  ///////////////// ARTIST //////////////////
   
  $artist_contract_config = array(
    'id' => 'artist_contract_box',          // meta box id, unique per meta box
    'title' => 'Artist Contract Box',          // meta box title
    'pages' => array('event_category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );

  $artist_contract_meta = new Tax_Meta_Class($artist_contract_config);
  $artist_contract_meta->addWysiwyg($prefix.'smlouva_spoluprace',array('name'=> __('Smlouva o spolupráci - obsah ','tax-meta')));
  $artist_contract_meta->addWysiwyg($prefix.'smlouva_prodej',array('name'=> __('Smlouva o prodeji - obsah ','tax-meta')));
    
  $artist_contract_meta->Finish();

}


function tcct_cely_nazev_udalosti($fields) {
    $fields[] = array(
        'field_name' => 'tcct_cely_nazev_udalosti',
        'field_title' => __('Celý název události (do smlouvy)', 'tcct'),
        'field_type' => 'text',
        'table_visibility' => false,
        'post_field_type' => 'post_meta'
    );

    return $fields;
}

add_filter( 'tc_event_fields', 'tcct_cely_nazev_udalosti', 10, 1 );