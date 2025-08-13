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
                return (method && method.carrier_code === 'monta');
            }, this);

            return this;
        },
    });
});
