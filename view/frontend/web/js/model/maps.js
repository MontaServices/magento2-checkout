define([
    'Magento_PageBuilder/js/utils/map'
], function (GoogleMap) {
    'use strict';
    return {
        // @var AdvancedMarkerElement[]
        allPickups: [],
        // On Map view, show only one Pickuppoint
        selectedPickupKey: 0,
        // Google Map component
        gmap: null,

        /** Instantiate a Google Map
         *
         * @param mapElement - HTML element to attach Google Map component on
         * @param customerLocation - Address of customer to center map on
         * @returns {null}
         */
        showMap: function (mapElement, customerLocation) {
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
                this.gmap = new GoogleMap(mapElement, this.allPickups, mapOptions);
            } else {
                console.warn("Could not instantiate Map, element not found.");
            }
            return this.gmap;
        },

        /** Return a single Pickup
         *
         * @param incrementer - change index to retrieve from there
         * @returns {*}
         */
        getPickup: function (incrementer = 0) {
            // If a pickup exists at that index
            if (this.allPickups.hasOwnProperty(this.selectedPickupKey + incrementer)) {
                // Increment index to retrieve different pickup
                this.selectedPickupKey += incrementer;
            }
            // Otherwise return again the same item
            return this.allPickups[this.selectedPickupKey];
        },

        /** Convert PickupPoints to Maps Markers
         *
         * @param pickupOptions
         */
        setPickupMarkers: function (pickupOptions) {
            let markers = [];
            pickupOptions.forEach(function (pickup) {
                // TODO $position should be a LatLng object
                pickup.position = null;
                // Attempt to copy all properties
                let marker = Object.assign(new google.maps.marker.AdvancedMarkerElement(), pickup);
                marker.map = self.gmap;
                marker.title = pickup.displayName;
                // marker.content = pickup.formattedAddress; // TODO Errors when setting
                marker.gmpClickable = true;
                // TODO open infoWindow on click
                markers.push(pickup);
            })
            this.allPickups = markers;
        }
    };

});