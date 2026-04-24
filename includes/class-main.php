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
            
            // Initialize GitHub Updater
            $updater = new Admin\Updater('akbarbahaulloh', 'meowfield', 'main');
            $updater->init();
        }
        new Meta_Boxes();
        new Shortcodes();
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
    }
}
