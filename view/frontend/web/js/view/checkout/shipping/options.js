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
                    if (options && options.DeliveryOptions?.length > 0) {
                        this.setOptions(options);

                        // Preselect first day on init
                        this.selectDay(options.DeliveryOptions[0]);

                        // Show block now
                        return true;
                    }
                }
                return false;
            }, this);

            return this;
        },


        // Called from the template when a day tile is clicked
        selectDay: function (option, event) {
            if (event) {
                let selectedTileStyle = "bg-blue-50 border-blue-400";
                // TODO style this tile as selected (same style as hover); option.classes does not update in UI
                // option.additionalClasses = selectedTileStyle;
                // event.target.addClass(selectedTileStyle);
            }
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
                    self[key] = responseProp;
                }
            });
        }.bind(this),
    });
});