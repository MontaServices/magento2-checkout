define([
    'Magento_PageBuilder/js/utils/map'
], function (GoogleMap) {
    'use strict';
    return {
        showMap: function (mapElement) {
            let gmap = null;
            if (mapElement) {
                let mapOptions = {
                    zoom: 12,
                    // TODO center should be user location obviously
                    center: new google.maps.LatLng(52.370216, 4.895168),
                    mapTypeControl: false
                };
                // Instantiate map on this element
                gmap = new GoogleMap(mapElement, self.PickupOptions(), mapOptions).bind(this);
            } else {
                console.warn("Could not instantiate Map, element not found.");
            }
            return gmap;
        },
    };

});