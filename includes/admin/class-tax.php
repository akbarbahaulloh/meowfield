<?php
namespace MeowField\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Tax {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_meowfield_tax', [$this, 'save_tax_config']);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'meowfield-tax-builder',
            __('Taxonomy Settings', 'meowfield'),
            [$this, 'render_builder'],
            'meowfield_tax',
            'normal',
            'high'
        );
    }

    public function render_builder($post) {
        $config = get_post_meta($post->ID, '_meowfield_tax_config', true);
        if (!$config) {
            $config = [
                'slug' => '',
                'plural' => '',
                'singular' => '',
                'hierarchical' => 1,
                'post_types' => []
            ];
        }
        
        $available_post_types = get_post_types(['public' => true], 'objects');

        include MEOWFIELD_PATH . 'includes/admin/views/tax-builder.php';
    }

    public function save_tax_config($post_id) {
        if (!isset($_POST['meowfield_tax_nonce']) || !wp_verify_nonce($_POST['meowfield_tax_nonce'], 'meowfield_save_tax')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['meowfield_tax'])) {
            $data = $_POST['meowfield_tax'];
            
            $config = [
                'slug' => sanitize_title($data['slug']),
                'plural' => sanitize_text_field($data['plural']),
                'singular' => sanitize_text_field($data['singular']),
                'hierarchical' => isset($data['hierarchical']) ? 1 : 0,
                'post_types' => isset($data['post_types']) ? array_map('sanitize_text_field', $data['post_types']) : [],
            ];

            update_post_meta($post_id, '_meowfield_tax_config', $config);
            
            update_option('meowfield_flush_rewrite_rules', true);
        }
    }
}
