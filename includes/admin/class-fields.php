<?php
namespace MeowField\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Fields {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_meowfield_group', [$this, 'save_field_group']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets($hook) {
        global $post;
        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ($post && $post->post_type === 'meowfield_group') {
                wp_enqueue_style('meowfield-admin', MEOWFIELD_URL . 'assets/css/admin.css', [], time());
                wp_enqueue_script('meowfield-admin', MEOWFIELD_URL . 'assets/js/admin.js', ['jquery', 'jquery-ui-sortable'], time(), true);
                
                wp_localize_script('meowfield-admin', 'meowfield', [
                    'nonce' => wp_create_nonce('meowfield_nonce'),
                    'i18n' => [
                        'confirm_delete' => __('Are you sure you want to remove this field?', 'meowfield'),
                    ]
                ]);
            }
        }
    }

    public function add_meta_boxes() {
        add_meta_box(
            'meowfield-builder',
            __('Fields', 'meowfield'),
            [$this, 'render_builder'],
            'meowfield_group',
            'normal',
            'high'
        );

        add_meta_box(
            'meowfield-location',
            __('Location', 'meowfield'),
            [$this, 'render_location'],
            'meowfield_group',
            'normal',
            'high'
        );
    }

    public function render_builder($post) {
        $fields = get_post_meta($post->ID, '_meowfield_fields', true);
        if (!$fields) {
            $fields = [];
        }
        
        include MEOWFIELD_PATH . 'includes/admin/views/builder.php';
    }

    public function render_location($post) {
        $rules = get_post_meta($post->ID, '_meowfield_rules', true);
        if (!$rules) {
            $rules = ['post_type' => 'post'];
        }
        
        include MEOWFIELD_PATH . 'includes/admin/views/location.php';
    }

    public function save_field_group($post_id) {
        if (!isset($_POST['meowfield_fields_nonce']) || !wp_verify_nonce($_POST['meowfield_fields_nonce'], 'meowfield_save_fields')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['meowfield_fields'])) {
            $fields = $_POST['meowfield_fields'];
            // Sanitize field names and keys
            $sanitized_fields = [];
            foreach ($fields as $field) {
                if (empty($field['label'])) continue;
                $sanitized_fields[] = [
                    'label' => sanitize_text_field($field['label']),
                    'name'  => sanitize_title($field['name'] ?: $field['label']),
                    'type'  => sanitize_text_field($field['type']),
                    'key'   => sanitize_text_field($field['key']),
                    'required' => isset($field['required']) ? 1 : 0,
                    'options' => isset($field['options']) ? $field['options'] : [],
                ];
            }
            update_post_meta($post_id, '_meowfield_fields', $sanitized_fields);
        }

        if (isset($_POST['meowfield_rules'])) {
            update_post_meta($post_id, '_meowfield_rules', $_POST['meowfield_rules']);
        }
    }
}
