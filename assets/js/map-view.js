(function($) {
    'use strict';

    const MeowMapView = {
        instances: {},

        init: function() {
            $('.mf-map-view-container').each(function() {
                MeowMapView.initInstance($(this));
            });
        },

        initInstance: function($container) {
            const mapId = $container.data('map-id');
            const canvasId = 'mf-map-view-' + mapId;
            const $canvas = $container.find('.mf-map-view-canvas');
            
            if (!$canvas.length) return;

            const map = L.map(canvasId).setView([-0.789275, 113.921327], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const instance = {
                map: map,
                markerGroup: L.featureGroup().addTo(map),
                $container: $container,
                mapId: mapId,
                timer: null
            };

            this.instances[mapId] = instance;

            // Initial load
            this.fetchMarkers(mapId);

            // Event listeners
            $container.on('change', '.mf-map-view-filter', function() {
                MeowMapView.fetchMarkers(mapId);
            });

            $container.on('keyup', '.mf-map-view-search', function() {
                clearTimeout(instance.timer);
                instance.timer = setTimeout(function() {
                    MeowMapView.fetchMarkers(mapId);
                }, 500);
            });
        },

        fetchMarkers: function(mapId) {
            const instance = this.instances[mapId];
            const $filters = instance.$container.find('.mf-map-view-filter');
            const search = instance.$container.find('.mf-map-view-search').val() || '';
            
            const taxonomies = {};
            $filters.each(function() {
                taxonomies[$(this).data('taxonomy')] = $(this).val();
            });

            instance.$container.addClass('mf-loading');

            $.ajax({
                url: meowfield_map_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'meowfield_get_map_data',
                    nonce: meowfield_map_ajax.nonce,
                    map_id: mapId,
                    search: search,
                    taxonomies: taxonomies
                },
                success: function(response) {
                    if (response.success) {
                        MeowMapView.updateMarkers(mapId, response.data);
                    }
                },
                complete: function() {
                    instance.$container.removeClass('mf-loading');
                }
            });
        },

        updateMarkers: function(mapId, markersData) {
            const instance = this.instances[mapId];
            const map = instance.map;
            const markerGroup = instance.markerGroup;

            markerGroup.clearLayers();
            const bounds = [];

            if (markersData && markersData.length > 0) {
                markersData.forEach(function(m) {
                    const marker = L.marker([m.lat, m.lng]);
                    marker.bindPopup('<div><a href="' + m.url + '">' + m.title + '</a></div>');
                    markerGroup.addLayer(marker);
                    bounds.push([m.lat, m.lng]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [40, 40] });
                }
            } else {
                // If no markers, maybe reset to a default view or center?
                // For now, do nothing or show a message.
            }
        }
    };

    $(document).ready(function() {
        if (typeof L !== 'undefined') {
            MeowMapView.init();
        }
    });

})(jQuery);
