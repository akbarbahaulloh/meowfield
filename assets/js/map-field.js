(function($) {
    'use strict';

    const MeowMap = {
        init: function() {
            $('.mf-map-input-wrapper').each(function() {
                MeowMap.initMap($(this));
            });
        },

        initMap: function($wrapper) {
            const $canvas = $wrapper.find('.mf-map-canvas');
            const $input = $wrapper.find('.mf-map-value');
            let val = $input.val();
            
            let lat = -6.200000; // Default Jakarta
            let lng = 106.816666;
            let zoom = 13;

            if (val) {
                try {
                    const data = JSON.parse(val);
                    lat = data.lat;
                    lng = data.lng;
                    zoom = data.zoom || 13;
                } catch(e) {
                    // fallback if not JSON
                    const parts = val.split(',');
                    if (parts.length >= 2) {
                        lat = parseFloat(parts[0]);
                        lng = parseFloat(parts[1]);
                    }
                }
            }

            const map = L.map($canvas[0]).setView([lat, lng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                MeowMap.updateValue($input, position.lat, position.lng, map.getZoom());
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                MeowMap.updateValue($input, e.latlng.lat, e.latlng.lng, map.getZoom());
            });

            map.on('zoomend', function() {
                const position = marker.getLatLng();
                MeowMap.updateValue($input, position.lat, position.lng, map.getZoom());
            });
        },

        updateValue: function($input, lat, lng, zoom) {
            const data = {
                lat: lat,
                lng: lng,
                zoom: zoom
            };
            $input.val(JSON.stringify(data));
        }
    };

    $(document).ready(function() {
        if (typeof L !== 'undefined') {
            MeowMap.init();
        }
    });

})(jQuery);
