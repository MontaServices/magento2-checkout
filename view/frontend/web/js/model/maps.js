define([
    'Magento_PageBuilder/js/utils/map'
], function (GoogleMap) {
    'use strict';
    return {
        /** Instantiate a Google Map
         *
         * @param mapElement - HTML element to attach Google Map component on
         * @param customerLocation - Address of customer to center map on
         * @returns {null}
         */
        showMap: function (mapElement, customerLocation) {
            let gmap = null;
            if (mapElement) {
                let mapOptions = {
                    // center map on customer address
                    center: new google.maps.LatLng(
                        parseFloat(customerLocation.latitude),
                        parseFloat(customerLocation.longitude)
                    ),
                    async: true,
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