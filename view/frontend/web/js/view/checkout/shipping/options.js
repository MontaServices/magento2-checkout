define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
], function (Component, ko, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Monta_Checkout/checkout/shipping/options'
        },

        initObservable: function () {
            this.isSelected = ko.computed(function () {
                let method = quote.shippingMethod();
                if (method) {
                    let options = JSON.parse(method.extension_attributes?.monta_shipping_options);
                    console.log("quote information: ", options);
                }
                // TODO do something with quote.shippingMethod.extension_attributes now available here
                return (method && method.carrier_code === 'monta');
            }, this);

            return this;
        },
    });
});
