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
            template: 'Monta_Checkout/checkout/shipping/options'
        },

        initObservable: function () {
            this.isSelected = ko.computed(function () {
                // Self references this component instead of Window
                self = this;
                let method = quote.shippingMethod();
                if (method && method.carrier_code === 'monta') {
                    let options = JSON.parse(method.extension_attributes?.monta_shipping_options);
                    if (options) {
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
            Object.entries(options).forEach(([key, option]) => {
                if (self.hasOwnProperty(key)) {
                    switch (key) {
                        case "DeliveryOptions":
                            // TODO set one of the options to show
                            self.SelectedDateOptions = option[1].options;
                            break;
                    }

                    self[key] = option;
                }
            })
        }.bind(this),
    });
});
