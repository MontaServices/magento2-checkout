define([
    'Magento_PageBuilder/js/utils/map'
], function (GoogleMap) {
    'use strict';
    return {
        /** Instantiate a Google Map
         *
         * @param mapElement
         * @param customerLocation - Address of customer to center map on
         * @returns {null}
         */
        showMap: function (mapElement, customerLocation) {
            let gmap = null;
            if (mapElement) {
                let mapOptions = {
                    zoom: 12,
                    // center map on user house
                    center: new google.maps.LatLng(
                        customerLocation.latitude,
                        customerLocation.longitude
                    ),
                    mapTypeControl: false
                };
                // Instantiate map on this element
                gmap = new GoogleMap(mapElement, self.PickupOptions(), mapOptions);
            } else {
                console.warn("Could not instantiate Map, element not found.");
            }
            return gmap;
        },
    };

});