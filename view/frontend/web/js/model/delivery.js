define([
    'Monta_Checkout/js/model/utility'
], function (utility) {
    'use strict';

    return {
        /**
         * Array of selected shipper options
         */
        shipperOptions: [],

        /** Mark the clicked day as select and unselect siblings
         *
         * @param option - Timeframe object that was selected
         * @param element - or the HTML element that was clicked
         * @returns {*[]}
         */
        selectDay: function (option, element) {
            if (element.classList) {
                this.focusSelection(element);
            }
            // option is the day, options is the list of carriers for that day
            return option.options ?? [];
        },

        selectDeliveryOption: function (option, quote) {
            this.updateQuote(quote, option);

            // Return TRUE to propagate default click behavior
            return true;
        },

        clickShipperOption: function (shipperOption, event, quote) {
            // When checkbox was checked
            if (event.target.checked) {
                // Box was checked, add this shipperOption (if not already in list)
                if (this.shipperOptions.filter(e => e.code === shipperOption.code).length < 1) {
                    this.shipperOptions.push(shipperOption);
                }
            } else {
                // Remove this shipperOption by code
                this.shipperOptions = this.shipperOptions.filter(e => e.code !== shipperOption.code);
            }

            // After updating shipperOptions, update quote
            this.updateQuote(quote);

            return true;
        },

        /** Update quote with selected ShippingOption and/or shipperOptions
         *
         * @param quote
         * @param option - Selected option, or keep empty to reuse existing saved one
         */
        updateQuote(quote, option = null) {
            // Set selected option code in quote as string
            let address = quote.shippingAddress();
            // create extension attributes if not present
            if (address['extension_attributes'] === undefined) {
                address['extension_attributes'] = {};
            }
            // When Option was not passed, presume it's an update and Option was already set
            if (!option && address['extension_attributes']['shipping_option']) {
                option = JSON.parse(address['extension_attributes']['shipping_option']);
            }

            if (option) {
                // Set to quote
                // TODO Unless $option is a PickupOption, which cannot have shipperoptions (already handled in backend tho)
                option.shipperOptions = this.shipperOptions;

                // Save the selected Option object to address
                address['extension_attributes']['shipping_option'] = JSON.stringify(option);
            } else {
                console.warn("Something went wrong, no option present to update");
            }
        },

        focusSelection: function (element) {
            // Apply blue focus to this element
            utility.applyAmongSiblings(element, ["monta-blue-focus"]);
        },
    }
});