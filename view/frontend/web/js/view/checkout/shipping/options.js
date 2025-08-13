define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Monta_Checkout/checkout/shipping/options'
        },
        init: function () {
            console.log('init shipping options');
        }
    });
});
