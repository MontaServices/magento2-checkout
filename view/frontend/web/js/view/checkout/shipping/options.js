define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Monta_Checkout/js/model/delivery',
    'Monta_Checkout/js/model/utility'
], function (Component, ko, quote, delivery, utility) {
    'use strict';

    return Component.extend({
        defaults: {
            DeliveryOptions: ko.observableArray([]),
            PickupOptions: ko.observableArray([]),
            SelectedDateOptions: ko.observableArray([]),
            template: 'Monta_Checkout/checkout/shipping/options',
            dayButtonIdentifier: 'selectDayButton',
            shippingOptionIdentifier: 'selectShippingOption',
            tabButtons: [
                {
                    id: 'delivery',
                    label: 'Bezorgen',
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
                {
                    id: 'pickup',
                    label: 'Afhalen',
                    callback: function (tabButton, event) {
                        utility.clickMode(this.id, event);
                    },
                },
            ],
        },

        initialize: function () {
            // Unsure how, but this is necessary for the observables to work
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
                    if (options && options.DeliveryOptions?.length > 0) {
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
            // Update observable array to trigger re-render
            self.SelectedDateOptions(
                // extract delivery options from the selected day
                delivery.selectDay(option, event, element)
            );
        },

        // Called from the template when a delivery option is clicked
        clickDeliveryOption: function (option, event) {
            // Focus on the selected option
            delivery.focusSelection(event.currentTarget);
            // Select this day in quote
            return delivery.selectDeliveryOption(option, quote);
        },

        selectFirst: function (element) {
            return utility.selectFirst(element);
        },

        setOptions: function (options) {
            // Copy each response property to component properties
            Object.entries(options).forEach(([key, responseProp]) => {
                if (self.hasOwnProperty(key)) {
                    // Update observable property with new data
                    switch (typeof self[key]) {
                        // ObservableArrays have this typeof
                        case 'function':
                            self[key](responseProp);
                            break;
                        default:
                            // otherwise just set the property
                            self[key] = responseProp;
                    }
                }
            });
        }.bind(this),
    });
});