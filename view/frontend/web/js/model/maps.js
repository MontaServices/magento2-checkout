define([
    'Magento_PageBuilder/js/utils/map'
], function (map) {
    'use strict';
    return {
        showMap: function (mapElement) {
            let gmap = null;
            if (mapElement) {
                let mapOptions = {
                    zoom: 12,
                    center: new google.maps.LatLng(52.370216, 4.895168),
                    mapTypeControl: false
                };
                gmap = map(mapElement, self.PickupOptions());
            } else {
                console.warn("Could not instantiate Map, element not found.");
            }
            return gmap;
        },
    };

});