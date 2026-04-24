<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Main {
    public function __construct() {
        $this->init_hooks();
        $this->load_components();
    }

    private function init_hooks() {
        add_action('init', [$this, 'register_post_types']);
    }

    private function load_components() {
        if (is_admin()) {
            new Admin\Fields();
            new Admin\Cpt();
            new Admin\Tax();
            new Admin\Map_View();
            
            // Initialize GitHub Updater
            $updater = new Admin\Updater('akbarbahaulloh', 'meowfield', 'main');
            $updater->init();
        }
        new Registrations();
        new Meta_Boxes();
        new Shortcodes();
        new Ajax();
    }

    public function register_post_types() {
        $labels = [
            'name'               => _x('Field Groups', 'post type general name', 'meowfield'),
            'singular_name'      => _x('Field Group', 'post type singular name', 'meowfield'),
            'menu_name'          => _x('MeowField', 'admin menu', 'meowfield'),
            'name_admin_bar'     => _x('Field Group', 'add new on admin bar', 'meowfield'),
            'add_new'            => _x('Add New', 'field group', 'meowfield'),
            'add_new_item'       => __('Add New Field Group', 'meowfield'),
            'new_item'           => __('New Field Group', 'meowfield'),
            'edit_item'          => __('Edit Field Group', 'meowfield'),
            'view_item'          => __('View Field Group', 'meowfield'),
            'all_items'          => __('All Field Groups', 'meowfield'),
            'search_items'       => __('Search Field Groups', 'meowfield'),
            'parent_item_colon'  => __('Parent Field Groups:', 'meowfield'),
            'not_found'          => __('No field groups found.', 'meowfield'),
            'not_found_in_trash' => __('No field groups found in Trash.', 'meowfield')
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'meowfield_group'],
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 80,
            'menu_icon'          => 'dashicons-welcome-widgets-menus',
            'supports'           => ['title'],
            'show_in_rest'       => false,
        ];

        register_post_type('meowfield_group', $args);

        // Register meowfield_cpt (hidden, for Post Type builder)
        $args_cpt = $args;
        $args_cpt['labels'] = [
            'name'               => _x('Post Types', 'post type general name', 'meowfield'),
            'singular_name'      => _x('Post Type', 'post type singular name', 'meowfield'),
            'menu_name'          => _x('Post Types', 'admin menu', 'meowfield'),
            'add_new'            => _x('Add New', 'post type', 'meowfield'),
            'add_new_item'       => __('Add New Post Type', 'meowfield'),
            'new_item'           => __('New Post Type', 'meowfield'),
            'edit_item'          => __('Edit Post Type', 'meowfield'),
            'view_item'          => __('View Post Type', 'meowfield'),
            'all_items'          => __('Post Types', 'meowfield'),
        ];
        $args_cpt['show_in_menu'] = 'edit.php?post_type=meowfield_group';
        register_post_type('meowfield_cpt', $args_cpt);

        // Register meowfield_tax (hidden, for Taxonomy builder)
        $args_tax = $args;
        $args_tax['labels'] = [
            'name'               => _x('Taxonomies', 'post type general name', 'meowfield'),
            'singular_name'      => _x('Taxonomy', 'post type singular name', 'meowfield'),
            'menu_name'          => _x('Taxonomies', 'admin menu', 'meowfield'),
            'add_new'            => _x('Add New', 'taxonomy', 'meowfield'),
            'add_new_item'       => __('Add New Taxonomy', 'meowfield'),
            'new_item'           => __('New Taxonomy', 'meowfield'),
            'edit_item'          => __('Edit Taxonomy', 'meowfield'),
            'view_item'          => __('View Taxonomy', 'meowfield'),
            'all_items'          => __('Taxonomies', 'meowfield'),
        ];
        $args_tax['show_in_menu'] = 'edit.php?post_type=meowfield_group';
        register_post_type('meowfield_tax', $args_tax);

        // Register meowfield_map (hidden, for Map View builder)
        $args_map = $args;
        $args_map['labels'] = [
            'name'               => _x('Map Views', 'post type general name', 'meowfield'),
            'singular_name'      => _x('Map View', 'post type singular name', 'meowfield'),
            'menu_name'          => _x('Map Views', 'admin menu', 'meowfield'),
            'add_new'            => _x('Add New', 'map view', 'meowfield'),
            'add_new_item'       => __('Add New Map View', 'meowfield'),
            'new_item'           => __('New Map View', 'meowfield'),
            'edit_item'          => __('Edit Map View', 'meowfield'),
            'view_item'          => __('View Map View', 'meowfield'),
            'all_items'          => __('Map Views', 'meowfield'),
        ];
        $args_map['show_in_menu'] = 'edit.php?post_type=meowfield_group';
        register_post_type('meowfield_map', $args_map);
    }
}
