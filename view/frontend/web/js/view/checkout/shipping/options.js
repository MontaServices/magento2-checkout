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
            template: 'Monta_Checkout/checkout/shipping/options',
            dayButtonIdentifier: 'selectDayButton',
            shippingOptionIdentifier: 'selectShippingOption',
        },

        initialize: function () {
            this._super().initObservable();
            return this;
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

        /**
         * Select the first item in the list
         *
         * @param element
         */
        selectFirst: function (element) {
            // use timeout to wait for next click when all elements are rendered
            setTimeout(function () {
                // Find first child element and click it
                let firstChild = element.querySelector("*:first-of-type");
                if (firstChild) {
                    firstChild.click();
                }
            }, 0);
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