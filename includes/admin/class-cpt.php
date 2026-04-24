<?php
namespace MeowField\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Cpt {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_meowfield_cpt', [$this, 'save_cpt_config']);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'meowfield-cpt-builder',
            __('Post Type Settings', 'meowfield'),
            [$this, 'render_builder'],
            'meowfield_cpt',
            'normal',
            'high'
        );
    }

    public function render_builder($post) {
        $config = get_post_meta($post->ID, '_meowfield_cpt_config', true);
        if (!$config) {
            $config = [
                'slug' => '',
                'plural' => '',
                'singular' => '',
                'public' => 1,
                'has_archive' => 1,
                'hierarchical' => 0,
                'supports' => ['title', 'editor', 'thumbnail']
            ];
        }
        
        include MEOWFIELD_PATH . 'includes/admin/views/cpt-builder.php';
    }

    public function save_cpt_config($post_id) {
        if (!isset($_POST['meowfield_cpt_nonce']) || !wp_verify_nonce($_POST['meowfield_cpt_nonce'], 'meowfield_save_cpt')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['meowfield_cpt'])) {
            $data = $_POST['meowfield_cpt'];
            
            $config = [
                'slug' => sanitize_title($data['slug']),
                'plural' => sanitize_text_field($data['plural']),
                'singular' => sanitize_text_field($data['singular']),
                'public' => isset($data['public']) ? 1 : 0,
                'has_archive' => isset($data['has_archive']) ? 1 : 0,
                'hierarchical' => isset($data['hierarchical']) ? 1 : 0,
                'supports' => isset($data['supports']) ? array_map('sanitize_text_field', $data['supports']) : [],
            ];

            update_post_meta($post_id, '_meowfield_cpt_config', $config);
            
            // Rewrite rules flush flag (to be handled later ideally, but calling it here is a quick fix, though heavy. Better to set an option to flush on next load)
            update_option('meowfield_flush_rewrite_rules', true);
        }
    }
}
