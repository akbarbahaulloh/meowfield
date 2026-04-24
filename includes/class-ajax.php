<?php
namespace MeowField;

if (!defined('ABSPATH')) {
    exit;
}

class Ajax {
    public function __construct() {
        add_action('wp_ajax_meowfield_get_map_data', [$this, 'get_map_data']);
        add_action('wp_ajax_nopriv_meowfield_get_map_data', [$this, 'get_map_data']);
    }

    public function get_map_data() {
        check_ajax_referer('meowfield_map_nonce', 'nonce');

        $map_id = intval($_POST['map_id']);
        $search = sanitize_text_field($_POST['search']);
        $tax_filters = isset($_POST['taxonomies']) ? $_POST['taxonomies'] : [];

        $settings = get_post_meta($map_id, '_meowfield_map_settings', true);
        if (!$settings) {
            wp_send_json_error('Map settings not found.');
        }

        $args = [
            'post_type'      => $settings['post_type'] === 'any' ? 'any' : $settings['post_type'],
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            's'              => $search,
            'meta_query'     => [
                [
                    'key'     => $settings['map_field'],
                    'compare' => 'EXISTS'
                ]
            ]
        ];

        $tax_query = [];
        if (!empty($tax_filters)) {
            foreach ($tax_filters as $tax_slug => $term_slug) {
                if (!empty($term_slug)) {
                    $tax_query[] = [
                        'taxonomy' => $tax_slug,
                        'field'    => 'slug',
                        'terms'    => $term_slug
                    ];
                }
            }
        }

        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $query = new \WP_Query($args);
        $markers = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $val = get_post_meta(get_the_ID(), $settings['map_field'], true);
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

        wp_send_json_success($markers);
    }
}
