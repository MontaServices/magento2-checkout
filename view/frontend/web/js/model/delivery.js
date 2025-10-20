define([
    'Monta_Checkout/js/model/utility'
], function (utility) {
    'use strict';

    // Selected state
    let selectedTileStyle = ["monta-blue-focus"];

    return {
        /** Mark the clicked day as select and unselect siblings
         *
         * @param option - Timeframe object that was selected
         * @param event - Click event
         * @param element - or the HTML element that was clicked
         * @returns {*[]}
         */
        selectDay: function (option, event, element) {
            // Pass click event or element
            let el = event?.currentTarget ?? element;
            if (el.classList) {
                this.focusSelection(el);
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
            address['extension_attributes']['shipping_option'] = option.code;

            // Return TRUE to propagate default click behavior
            return true;
        },

        focusSelection: function (element) {
            utility.applyAmongSiblings(element, [selectedTileStyle]);
        },


    }
});