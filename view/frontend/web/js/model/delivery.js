define([
    'Monta_Checkout/js/model/utility'
], function (utility) {
    'use strict';

    return {
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
            // Set selected option code in quote as string
            let address = quote.shippingAddress();
            // create extension attributes if not present
            if (address['extension_attributes'] === undefined) {
                address['extension_attributes'] = {};
            }

            // Save the selected Option object to address
            // TODO What about selected ShipperOptions?
            address['extension_attributes']['shipping_option'] = JSON.stringify(option);

            // Return TRUE to propagate default click behavior
            return true;
        },

        focusSelection: function (element) {
            // Apply blue focus to this element
            utility.applyAmongSiblings(element, ["monta-blue-focus"]);
        },


    }
});