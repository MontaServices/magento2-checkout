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
                this.gmap = new GoogleMap(mapElement, this.getMarkers(), mapOptions);
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

        /**
         *
         * @param pickupOptions
         */
        setPickupMarkers: function (pickupOptions) {
            this.allPickups = pickupOptions;
        },

        /** Get PickupPoints as Google Map markers
         *
         * @returns AdvancedMarkerElement[]
         */
        getMarkers: function () {
            // TODO I give up, just return the pickups objects array. These become Marker which is deprecated.
            return this.allPickups;

            // TODO get AdvancedMarkerElement to work to get rid of deprecated warning
            // https://developers.google.com/maps/documentation/javascript/advanced-markers/migration
            // library is present and loaded, but examples from documentation simply don't work
            let markers = [];
            this.allPickups.forEach(function (pickup) {
                // Add Marker element to markers array
                markers.push(
                    // new google.maps.Marker({
                    new google.maps.marker.AdvancedMarkerElement({
                        map: self.gmap,
                        title: pickup.displayName,
                        position: new google.maps.LatLng(
                            parseFloat(pickup.latitude),
                            parseFloat(pickup.longitude)
                        ),
                        gmpClickable: true,
                    })
                );
            })
            return markers;
        }
    };

});