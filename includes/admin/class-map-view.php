<?php
namespace MeowField\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Map_View {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_meowfield_map', [$this, 'save_map_view']);
        add_filter('manage_meowfield_map_posts_columns', [$this, 'add_shortcode_column']);
        add_action('manage_meowfield_map_posts_custom_column', [$this, 'render_shortcode_column'], 10, 2);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'meowfield-map-view-builder',
            __('Map View Builder', 'meowfield'),
            [$this, 'render_builder'],
            'meowfield_map',
            'normal',
            'high'
        );
    }

    public function render_builder($post) {
        $settings = get_post_meta($post->ID, '_meowfield_map_settings', true);
        if (!is_array($settings)) $settings = [];

        $defaults = [
            'post_type' => 'any',
            'map_field' => '',
            'taxonomies' => [],
            'enable_search' => 1,
            'height' => '500px'
        ];
        $settings = wp_parse_args($settings, $defaults);

        wp_nonce_field('meowfield_save_map_view', 'meowfield_map_view_nonce');
        
        include MEOWFIELD_PATH . 'includes/admin/views/map-view-builder.php';
    }

    public function save_map_view($post_id) {
        if (!isset($_POST['meowfield_map_view_nonce']) || !wp_verify_nonce($_POST['meowfield_map_view_nonce'], 'meowfield_save_map_view')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $settings = isset($_POST['map_view']) ? $_POST['map_view'] : [];
        
        $map_field = sanitize_text_field($settings['map_field']);
        // If user accidentally pasted the whole shortcode [meowfield_map name="lokasi"]
        if (preg_match('/name="([^"]+)"/', $map_field, $matches)) {
            $map_field = $matches[1];
        }

        // Sanitize
        $sanitized = [
            'post_type' => sanitize_text_field($settings['post_type']),
            'map_field' => $map_field,
            'taxonomies' => isset($settings['taxonomies']) ? array_map('sanitize_text_field', $settings['taxonomies']) : [],
            'enable_search' => isset($settings['enable_search']) ? 1 : 0,
            'height' => sanitize_text_field($settings['height'])
        ];

        update_post_meta($post_id, '_meowfield_map_settings', $sanitized);
    }

    public function add_shortcode_column($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['shortcode'] = __('Shortcode', 'meowfield');
            }
        }
        return $new_columns;
    }

    public function render_shortcode_column($column, $post_id) {
        if ($column === 'shortcode') {
            echo '<code>[meowfield_map_view id="' . $post_id . '"]</code>';
        }
    }
}
