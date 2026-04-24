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

            const $latInput = $wrapper.find('.mf-map-lat');
            const $lngInput = $wrapper.find('.mf-map-lng');
            const $searchBtn = $wrapper.find('.mf-map-search-btn');
            const $searchInput = $wrapper.find('.mf-map-search');

            const updateMapState = function(newLat, newLng, newZoom) {
                const z = newZoom || map.getZoom();
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], z);
                $latInput.val(newLat);
                $lngInput.val(newLng);
                MeowMap.updateValue($input, newLat, newLng, z);
            };

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                $latInput.val(position.lat);
                $lngInput.val(position.lng);
                MeowMap.updateValue($input, position.lat, position.lng, map.getZoom());
            });

            map.on('click', function(e) {
                updateMapState(e.latlng.lat, e.latlng.lng);
            });

            map.on('zoomend', function() {
                const position = marker.getLatLng();
                MeowMap.updateValue($input, position.lat, position.lng, map.getZoom());
            });

            // Handle coordinate manual input
            $latInput.on('change', function() {
                updateMapState(parseFloat($(this).val()) || 0, parseFloat($lngInput.val()) || 0);
            });
            $lngInput.on('change', function() {
                updateMapState(parseFloat($latInput.val()) || 0, parseFloat($(this).val()) || 0);
            });

            // Handle Search
            const performSearch = function() {
                const query = $searchInput.val();
                if (!query) return;

                $searchBtn.text('Mencari...').prop('disabled', true);
                
                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/search',
                    data: {
                        format: 'json',
                        q: query,
                        limit: 1
                    },
                    success: function(data) {
                        if (data && data.length > 0) {
                            updateMapState(parseFloat(data[0].lat), parseFloat(data[0].lon), 15);
                        } else {
                            alert('Lokasi tidak ditemukan.');
                        }
                    },
                    error: function() {
                        alert('Gagal menghubungi server pencarian.');
                    },
                    complete: function() {
                        $searchBtn.text('Cari').prop('disabled', false);
                    }
                });
            };

            $searchBtn.on('click', performSearch);
            $searchInput.on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    performSearch();
                }
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
