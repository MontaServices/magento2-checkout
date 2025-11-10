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
            pickupsIdentifier: 'pickup-points-list',
            showingMap: ko.observable(false),
            tabButtons: ko.observableArray([
                {
                    id: 'delivery',
                    label: 'Bezorgen',
                    active: true,
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
                {
                    id: 'pickup',
                    label: 'Afhalen',
                    active: true,
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
            ]),
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
            return self.getPickup(1);
        },
        prevPickup: function (component, event) {
            return self.getPickup(-1);
        },
        getPickup: function (incrementer) {
            // Get the next pickup from list
            let pickup = maps.getPickup(incrementer);
            // Update filtered list with only this pickup
            self.PickupOptions([pickup]);
            // TODO when next item does not exist, hide Next button
            // After updating the list, click the (only) first item to select this pickup
            let pickupsList = document.getElementById(self.pickupsIdentifier);
            if (pickupsList) {
                utility.clickFirst(pickupsList);
            }
        },


        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    // Update observable property with new data
                    switch (typeof self[key]) {
                        // ObservableArrays are typeof function
                        case 'function':
                            self[key](responseProp);
                            break;
                        default:
                            // otherwise just set the property
                            self[key] = responseProp;
                    }
                }
            });

            // Also keep PickupPoints in Maps component
            if (options.PickupOptions.length) {
                maps.setPickupMarkers(options.PickupOptions);
            }
        },
    });
});