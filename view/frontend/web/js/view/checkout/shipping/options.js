define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Monta_Checkout/js/model/delivery',
    'Monta_Checkout/js/model/utility',
    'Monta_Checkout/js/model/maps',
], function (Component, ko, quote, delivery, utility, maps) {
    'use strict';

    return Component.extend({
        defaults: {
            DeliveryOptions: ko.observableArray([]),
            // Filtered list of PickupPoints
            PickupOptions: ko.observableArray([]),
            SelectedDateOptions: ko.observableArray([]),
            CustomerLocation: {},
            template: 'Monta_Checkout/checkout/shipping/options',
            dayButtonIdentifier: 'selectDayButton',
            shippingOptionIdentifier: 'selectShippingOption',
            mapIdentifier: 'montaPickupsMap',
            pickupsIdentifier: 'pickupPointsList',
            showingMap: ko.observable(false),
            listButtonText: ko.observable(""),
        },

        initialize: function () {
            // This is necessary for the observables to work
            this._super().initObservable();
            return this;
        },

        initObservable: function () {
            // Determine if Monta popup should be shown
            this.isSelected = ko.computed(function () {
                // Self references this component instead of Window
                self = this;
                let method = quote.shippingMethod();
                if (method && method.carrier_code === 'monta') {
                    let options = JSON.parse(method.extension_attributes?.monta_shipping_options);
                    // If object is not empty
                    if (options && (options.DeliveryOptions?.length > 0 || options.PickupOptions?.length > 0)) {
                        this.setOptions(options);

                        // Show block now
                        return true;
                    }
                }
                return false;
            }, this);

            // Define this during initialize
            this.tabButtons = ko.observableArray([
                {
                    // id is also the name of the property this corresponds to
                    id: 'DeliveryOptions',
                    label: 'Bezorgen',
                    active: false,
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
                {
                    id: 'PickupOptions',
                    label: 'Afhalen',
                    active: false,
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
            ]);
            return this;
        },

        // Called from the template when a day tile is clicked
        clickDayTile: function (option, event, element) {
            let el = event?.currentTarget ?? element;
            // Update observable array to trigger re-render
            self.SelectedDateOptions(
                // extract delivery options from the selected day
                delivery.selectDay(option, el)
            );
        },

        // Called from the template when a delivery option is clicked
        clickDeliveryOption: function (option, event) {
            let element = event.currentTarget;
            if (element) {
                // Focus on the selected option
                delivery.focusSelection(element);

                // If this was a pickup, show business hours
                // TODO move identifier to property but child template pickup.html has no access
                let hoursSelector = ".business-hours";
                // Hide every Business Hours element
                utility.applyClasses(element.parentElement?.querySelectorAll(hoursSelector), ['hidden']);
                // Then show the current one
                utility.applyClasses(element.querySelector(hoursSelector), ['hidden'], false);
            }

            // Select this day in quote
            return delivery.selectDeliveryOption(option, quote);
        },

        /** Click the first child element of this element
         *
         * @param element
         * @returns {boolean|*}
         */
        clickFirst: function (element) {
            return utility.clickFirst(element);
        },

        /** Show the Pickups map
         *
         * @param component
         * @param event
         */
        toggleMap: function (component, event) {
            // Change mode between list and map
            self.showingMap(!self.showingMap());

            let mapElement = document.querySelector("#" + self.mapIdentifier);
            if (mapElement) {
                if (self.showingMap()) {
                    // Attach GoogleMap component to this element TODO only once
                    maps.showMap(mapElement, self.CustomerLocation);

                    // Update observableArray to show only the selected pickup
                    self.getPickup();
                } else {
                    // Show all pickups again
                    self.PickupOptions(maps.allPickups);
                }

                // Toggle map display accordingly
                utility.applyClasses(mapElement.parentElement, ['hidden'], !self.showingMap());
            }
        },

        /** Browse through pickuppoints on map
         *
         * @param component
         * @param event
         */
        nextPickup: function (component, event) {
            return self.getPickup(1, event);
        },
        prevPickup: function (component, event) {
            return self.getPickup(-1, event);
        },
        getPickup: function (incrementer, event = null) {
            // Get the next pickup from list
            let pickup = maps.getPickup(incrementer);
            // Update filtered list with only this pickup
            self.PickupOptions([pickup]);
            // After updating the list, click the (only) first item to select this pickup
            let pickupsList = document.getElementById(self.pickupsIdentifier);
            if (pickupsList) {
                utility.clickFirst(pickupsList);
            }
            // Index of selected key is one lower than the nominal index because arrays are zero-based
            let nth = maps.selectedPickupKey + 1;
            // Update text to show status
            self.listButtonText("%s van de %t afhaalpunten"
                .replace("%s", nth)
                .replace("%t", maps.allPickups.length)
            );

            /** Determine if this arrow button should be disabled */
            if (event) {
                // Add to this one to remove from siblings (every arrow button should enable their opposite)
                utility.applyAmongSiblings(event.currentTarget, ['text-gray-500'], true);

                // Then hide or show this one depending on next availability
                utility.applyClasses(event.currentTarget, ['text-gray-500'],
                    // when limit is reached OR this is the first one and we are going backwards
                    (nth >= maps.allPickups.length || (nth === 1 && incrementer < 1)));
            }
        },

        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    // Update observable property with new data
                    switch (typeof self[key]) {
                        // ObservableArrays have type function
                        case 'function':
                            // Update this observable property with this value
                            self[key](responseProp);

                            // When setting array, activate its corresponding Tab button
                            if (Array.isArray(responseProp) && responseProp.length) {
                                // Find button for this property key and activate it (catch empty with OR operator)
                                (self.tabButtons().find(button => button.id === key) || {}).active = true;
                            }
                            break;
                        default:
                            // otherwise just set the property
                            self[key] = responseProp;
                    }
                }
            });

            // Also keep PickupPoints in Maps component
            maps.setPickupMarkers(options.PickupOptions);
        },
    });
});