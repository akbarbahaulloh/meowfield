<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {
    public function __construct() {
        add_shortcode('meowfield', [$this, 'render_field']);
        add_shortcode('meowfield_map', [$this, 'render_map']);
    }

    public function render_field($atts) {
        $atts = shortcode_atts([
            'name' => '',
            'post_id' => get_the_ID(),
        ], $atts);

        if (empty($atts['name'])) return '';

        $value = get_post_meta($atts['post_id'], $atts['name'], true);
        
        if (is_array($value)) {
            return json_encode($value);
        }

        return esc_html($value);
    }

    public function render_map($atts) {
        $atts = shortcode_atts([
            'name' => '',
            'post_id' => get_the_ID(),
            'height' => '400px',
        ], $atts);

        if (empty($atts['name'])) return '';

        $value = get_post_meta($atts['post_id'], $atts['name'], true);
        if (!$value) return '';

        // Enqueue Leaflet
        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);

        $id = 'mf-map-' . uniqid();
        
        ob_start();
        ?>
        <div id="<?php echo $id; ?>" style="height:<?php echo esc_attr($atts['height']); ?>; width:100%; border-radius:8px; overflow:hidden;"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const data = <?php echo $value; ?>;
                const map = L.map('<?php echo $id; ?>').setView([data.lat, data.lng], data.zoom || 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                L.marker([data.lat, data.lng]).addTo(map);
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
