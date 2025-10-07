define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Monta_Checkout/js/model/delivery'
], function (Component, ko, quote, delivery) {
    'use strict';

    return Component.extend({
        defaults: {
            DeliveryOptions: ko.observableArray([]),
            PickupOptions: ko.observableArray([]),
            SelectedDateOptions: ko.observableArray([]),
            template: 'Monta_Checkout/checkout/shipping/options'
        },

        initialize: function () {
            // Create observables before _super (which applies bindings)
            this.SelectedDateOptions = ko.observableArray([]);
            return this._super().initObservable();
        },

        initObservable: function () {
            delivery.isSelected();
            // Determine if Monta popup should be shown
            this.isSelected = ko.computed(function () {
                // Self references this component instead of Window
                self = this;
                let method = quote.shippingMethod();
                if (method && method.carrier_code === 'monta') {
                    let options = JSON.parse(method.extension_attributes?.monta_shipping_options);
                    // If object is not empty
                    if (options && options.DeliveryOptions?.length > 0) {
                        this.setOptions(options);

                        // Preselect first day on init
                        this.clickDayTile(options.DeliveryOptions[0]);

                        // Show block now
                        return true;
                    }
                }
                return false;
            }, this);

            return this;
        },

        // Called from the template when a day tile is clicked
        clickDayTile: function (option, event) {
            // Update observable array to trigger re-render
            self.SelectedDateOptions(
                // extract delivery options from the selected day
                delivery.selectDay(option, event)
            );
        },

        // Called from the template when a delivery option is clicked
        clickDeliveryOption: function (option, event) {
            // Select this day in quote
            delivery.selectDeliveryOption(option, quote);
        },

        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    // Update observable property with new array
                    // TODO check if property is observable, or it will fail
                    self[key](responseProp);
                }
            });
        }.bind(this),
    });
});