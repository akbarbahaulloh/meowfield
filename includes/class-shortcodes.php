<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {
    public function __construct() {
        add_shortcode('meowfield', [$this, 'render_field']);
        add_shortcode('meowfield_map', [$this, 'render_map']);
        add_shortcode('meowfield_map_all', [$this, 'render_map_all']);
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

    public function render_map_all($atts) {
        $atts = shortcode_atts([
            'name' => '',
            'post_type' => 'any',
            'height' => '400px',
        ], $atts);

        if (empty($atts['name'])) return '';

        $args = [
            'post_type'      => $atts['post_type'] === 'any' ? 'any' : explode(',', $atts['post_type']),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => $atts['name'],
                    'compare' => 'EXISTS'
                ]
            ]
        ];

        $query = new \WP_Query($args);
        $markers = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $val = get_post_meta(get_the_ID(), $atts['name'], true);
                if ($val) {
                    $data = json_decode(stripslashes($val), true);
                    if ($data && isset($data['lat']) && isset($data['lng'])) {
                        $markers[] = [
                            'lat' => $data['lat'],
                            'lng' => $data['lng'],
                            'title' => get_the_title(),
                            'url' => get_permalink()
                        ];
                    }
                }
            }
            wp_reset_postdata();
        }

        if (empty($markers)) return '<p>No locations found.</p>';

        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4');
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true);

        $id = 'mf-map-all-' . uniqid();
        
        ob_start();
        ?>
        <div id="<?php echo $id; ?>" style="height:<?php echo esc_attr($atts['height']); ?>; width:100%; border-radius:8px; overflow:hidden; z-index: 1;"></div>
        <style>
            #<?php echo $id; ?> .leaflet-popup-content-wrapper {
                border-radius: 8px;
            }
            #<?php echo $id; ?> .leaflet-popup-content {
                margin: 15px 20px;
                font-family: inherit;
            }
            #<?php echo $id; ?> .leaflet-popup-content a {
                color: #2563eb;
                text-decoration: none;
                font-weight: bold;
                font-size: 14px;
            }
            #<?php echo $id; ?> .leaflet-popup-content a:hover {
                text-decoration: underline;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const markersData = <?php echo json_encode($markers); ?>;
                const map = L.map('<?php echo $id; ?>');
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                const bounds = [];
                
                markersData.forEach(function(m) {
                    const marker = L.marker([m.lat, m.lng]).addTo(map);
                    marker.bindPopup('<div><a href="' + m.url + '">' + m.title + '</a></div>');
                    bounds.push([m.lat, m.lng]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [40, 40] });
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
