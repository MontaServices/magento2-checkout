define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
], function (Component, ko, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            DeliveryOptions: [],
            PickupOptions: [],
            SelectedDateOptions: [],
            template: 'Monta_Checkout/checkout/shipping/options'
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
                    if (options && options.hasOwnProperty('DeliveryOptions')) {
                        this.setOptions(options);

                        // Show block now
                        return true;
                    }
                }
                return false;
            }, this);

            return this;
        },

        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    switch (key) {
                        case "DeliveryOptions":
                            // TODO do this when a day is selected, not here
                            self.SelectedDateOptions = responseProp[1].options;
                            break;
                    }

                    self[key] = responseProp;
                }
            })
        }.bind(this),
    });
});