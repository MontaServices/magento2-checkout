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
            SelectedDateOptions: ko.observableArray([]),
            template: 'Monta_Checkout/checkout/shipping/options'
        },

        initialize: function () {
            // Create observables before _super (which applies bindings)
            this.SelectedDateOptions = ko.observableArray([]);
            return this._super().initObservable();
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


        // Called from the template when a day tile is clicked
        selectDay: function (option) {
            // option is the day, options is the list of carriers for that day
            let opts = (option && option.options) ? option.options : [];
            // TODO why would option.options ever be empty?

            // Update observable array to trigger re-render
            self.SelectedDateOptions(opts);
        },

        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    switch (key) {
                        case "DeliveryOptions":
                            // TODO do this when a day is selected, not here
                            self.SelectedDateOptions(responseProp[1].options);
                            break;
                    }

                    self[key] = responseProp;
                }
            });
        }.bind(this),
    });
});