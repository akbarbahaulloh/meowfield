<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Registrations {
    public function __construct() {
        add_action('init', [$this, 'register_all'], 10);
    }

    public function register_all() {
        $this->register_taxonomies();
        $this->register_cpts();

        if (get_option('meowfield_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('meowfield_flush_rewrite_rules');
        }
    }

    private function register_taxonomies() {
        $taxes = get_posts([
            'post_type'      => 'meowfield_tax',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        foreach ($taxes as $tax) {
            $config = get_post_meta($tax->ID, '_meowfield_tax_config', true);
            if (empty($config['slug'])) continue;

            $slug = sanitize_title($config['slug']);
            $plural = isset($config['plural']) ? sanitize_text_field($config['plural']) : $tax->post_title;
            $singular = isset($config['singular']) ? sanitize_text_field($config['singular']) : $plural;
            $post_types = isset($config['post_types']) ? $config['post_types'] : ['post'];

            $labels = [
                'name'              => $plural,
                'singular_name'     => $singular,
                'search_items'      => 'Search ' . $plural,
                'all_items'         => 'All ' . $plural,
                'parent_item'       => 'Parent ' . $singular,
                'parent_item_colon' => 'Parent ' . $singular . ':',
                'edit_item'         => 'Edit ' . $singular,
                'update_item'       => 'Update ' . $singular,
                'add_new_item'      => 'Add New ' . $singular,
                'new_item_name'     => 'New ' . $singular . ' Name',
                'menu_name'         => $plural,
            ];

            $args = [
                'hierarchical'      => isset($config['hierarchical']) ? (bool) $config['hierarchical'] : true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => $slug],
                'show_in_rest'      => true,
            ];

            register_taxonomy($slug, $post_types, $args);
        }
    }

    private function register_cpts() {
        $cpts = get_posts([
            'post_type'      => 'meowfield_cpt',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        foreach ($cpts as $cpt) {
            $config = get_post_meta($cpt->ID, '_meowfield_cpt_config', true);
            if (empty($config['slug'])) continue;

            $slug = sanitize_title($config['slug']);
            $plural = isset($config['plural']) ? sanitize_text_field($config['plural']) : $cpt->post_title;
            $singular = isset($config['singular']) ? sanitize_text_field($config['singular']) : $plural;

            $labels = [
                'name'                  => $plural,
                'singular_name'         => $singular,
                'menu_name'             => $plural,
                'name_admin_bar'        => $singular,
                'add_new'               => 'Add New',
                'add_new_item'          => 'Add New ' . $singular,
                'new_item'              => 'New ' . $singular,
                'edit_item'             => 'Edit ' . $singular,
                'view_item'             => 'View ' . $singular,
                'all_items'             => 'All ' . $plural,
                'search_items'          => 'Search ' . $plural,
                'parent_item_colon'     => 'Parent ' . $plural . ':',
                'not_found'             => 'No ' . strtolower($plural) . ' found.',
                'not_found_in_trash'    => 'No ' . strtolower($plural) . ' found in Trash.',
            ];

            $args = [
                'labels'             => $labels,
                'public'             => isset($config['public']) ? (bool) $config['public'] : true,
                'publicly_queryable' => isset($config['public']) ? (bool) $config['public'] : true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => ['slug' => $slug],
                'capability_type'    => 'post',
                'has_archive'        => isset($config['has_archive']) ? (bool) $config['has_archive'] : true,
                'hierarchical'       => isset($config['hierarchical']) ? (bool) $config['hierarchical'] : false,
                'menu_position'      => null,
                'supports'           => isset($config['supports']) ? $config['supports'] : ['title', 'editor', 'thumbnail'],
                'show_in_rest'       => true,
            ];

            register_post_type($slug, $args);
        }
    }
}
