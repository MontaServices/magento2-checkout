define([
    'Magento_PageBuilder/js/utils/map'
], function (GoogleMap) {
    'use strict';
    return {
        allPickups: [],
        // On Map view, show only one Pickuppoint
        selectedPickupKey: 0,

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

        /** Return a single Pickup
         *
         * @param incrementer - change index to retrieve from there
         * @returns {*}
         */
        getPickup: function (incrementer) {
            // Increment key
            this.selectedPickupKey += incrementer;
            // Return pickup point at that index
            return this.allPickups[this.selectedPickupKey];
        }
    };

});