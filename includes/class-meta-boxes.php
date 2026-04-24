<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_values']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets($hook) {
        wp_enqueue_media();
        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);
        wp_enqueue_script('meowfield-map', MEOWFIELD_URL . 'assets/js/map-field.js', ['jquery', 'leaflet'], time(), true);
        wp_enqueue_script('meowfield-image', MEOWFIELD_URL . 'assets/js/image-field.js', ['jquery'], time(), true);
    }

    public function add_custom_meta_boxes($post_type) {
        $groups = $this->get_matching_groups($post_type);

        foreach ($groups as $group) {
            add_meta_box(
                'meowfield-group-' . $group->ID,
                $group->post_title,
                [$this, 'render_meta_box'],
                $post_type,
                'normal',
                'default',
                ['fields' => get_post_meta($group->ID, '_meowfield_fields', true)]
            );
        }
    }

    private function get_matching_groups($post_type) {
        $args = [
            'post_type'      => 'meowfield_group',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $groups = get_posts($args);
        $matching = [];

        foreach ($groups as $group) {
            $rules = get_post_meta($group->ID, '_meowfield_rules', true);
            if (isset($rules['value']) && $rules['value'] === $post_type) {
                $matching[] = $group;
            }
        }

        return $matching;
    }

    public function render_meta_box($post, $metabox) {
        $fields = $metabox['args']['fields'];
        if (empty($fields)) return;

        wp_nonce_field('meowfield_save_meta', 'meowfield_meta_nonce');

        echo '<div class="meowfield-fields-container">';
        foreach ($fields as $field) {
            $value = get_post_meta($post->ID, $field['name'], true);
            $this->render_field($field, $value);
        }
        echo '</div>';
    }

    private function render_field($field, $value) {
        $label = esc_html($field['label']);
        $name = esc_attr($field['name']);
        $type = $field['type'];
        $is_required = isset($field['required']) && $field['required'] ? true : false;
        $req_attr = $is_required ? 'required' : '';
        $req_mark = $is_required ? ' <span style="color:red;">*</span>' : '';

        echo '<div class="meowfield-field-row" style="margin-bottom: 15px;">';
        echo '<label style="display:block; font-weight:bold; margin-bottom:5px;">' . $label . $req_mark . '</label>';

        switch ($type) {
            case 'text':
                echo '<input type="text" name="mf[' . $name . ']" value="' . esc_attr($value) . '" style="width:100%;" ' . $req_attr . '>';
                break;
            case 'textarea':
                echo '<textarea name="mf[' . $name . ']" style="width:100%;" rows="4" ' . $req_attr . '>' . esc_textarea($value) . '</textarea>';
                break;
            case 'select':
                echo '<select name="mf[' . $name . ']" style="width:100%;" ' . $req_attr . '>';
                // Options logic can be added later
                echo '</select>';
                break;
            case 'image':
                $this->render_image_field($name, $value);
                break;
            case 'map':
                $this->render_map_field($name, $value);
                break;
        }

        echo '</div>';
    }

    private function render_image_field($name, $value) {
        $img_url = $value ? wp_get_attachment_image_url($value, 'thumbnail') : '';
        echo '<div class="mf-image-input-wrapper">';
        echo '<div class="mf-image-preview" style="margin-bottom:10px; width:100px; height:100px; border:1px solid #ddd; background:#f9f9f9; display:flex; align-items:center; justify-content:center;">';
        if ($img_url) {
            echo '<img src="' . esc_url($img_url) . '" style="max-width:100%; max-height:100%;">';
        } else {
            echo '<span class="dashicons dashicons-format-image" style="font-size:40px; width:40px; height:40px; color:#ccc;"></span>';
        }
        echo '</div>';
        echo '<input type="hidden" name="mf[' . $name . ']" value="' . esc_attr($value) . '" class="mf-image-value">';
        echo '<button type="button" class="button mf-select-image">Select Image</button> ';
        echo '<button type="button" class="button mf-remove-image" style="' . ($value ? '' : 'display:none;') . '">Remove</button>';
        echo '</div>';
    }

    private function render_map_field($name, $value) {
        $lat = '-6.200000';
        $lng = '106.816666';
        
        if (!empty($value)) {
            $data = json_decode(stripslashes($value), true);
            if ($data) {
                $lat = isset($data['lat']) ? $data['lat'] : $lat;
                $lng = isset($data['lng']) ? $data['lng'] : $lng;
            } else {
                $parts = explode(',', $value);
                if (count($parts) >= 2) {
                    $lat = trim($parts[0]);
                    $lng = trim($parts[1]);
                }
            }
        }

        echo '<div class="mf-map-input-wrapper" data-name="' . esc_attr($name) . '" style="border: 1px solid #ddd; background: #f9f9f9; padding: 10px; border-radius: 4px;">';
        
        // Search bar
        echo '<div style="margin-bottom: 10px; display: flex; gap: 5px;">';
        echo '<input type="text" class="mf-map-search" placeholder="Cari lokasi (contoh: Jakarta, Monas)" style="flex: 1; padding: 6px;">';
        echo '<button type="button" class="button mf-map-search-btn">Cari</button>';
        echo '</div>';
        
        // Map Canvas
        echo '<div class="mf-map-canvas" style="height:300px; background:#eee; border:1px solid #ddd; border-radius: 4px; margin-bottom: 10px;"></div>';
        
        // Coordinates and Value
        echo '<div style="display: flex; gap: 10px;">';
        echo '<div style="flex: 1;"><label style="display:block; font-size: 12px; font-weight: bold;">Latitude</label><input type="text" class="mf-map-lat" value="' . esc_attr($lat) . '" style="width: 100%;"></div>';
        echo '<div style="flex: 1;"><label style="display:block; font-size: 12px; font-weight: bold;">Longitude</label><input type="text" class="mf-map-lng" value="' . esc_attr($lng) . '" style="width: 100%;"></div>';
        echo '</div>';
        
        echo '<input type="hidden" name="mf[' . esc_attr($name) . ']" value="' . esc_attr($value) . '" class="mf-map-value">';
        echo '</div>';
    }

    public function save_meta_values($post_id) {
        if (!isset($_POST['meowfield_meta_nonce']) || !wp_verify_nonce($_POST['meowfield_meta_nonce'], 'meowfield_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['mf']) && is_array($_POST['mf'])) {
            foreach ($_POST['mf'] as $key => $value) {
                update_post_meta($post_id, sanitize_text_field($key), $value);
            }
        }
    }
}
