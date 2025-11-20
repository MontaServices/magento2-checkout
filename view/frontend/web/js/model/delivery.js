define([
    'Monta_Checkout/js/model/utility'
], function (utility) {
    'use strict';

    return {
        /**
         * Array of selected shipper options
         */
        extras: [],

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
            // TODO add this.extras to option object, first check if they belong to this option
            address['extension_attributes']['shipping_option'] = JSON.stringify(option);

            // Return TRUE to propagate default click behavior
            return true;
        },

        clickExtra: function (extra, event) {
            // When checkbox was checked
            // TODO click now runs twice, on <label> and on <input>
            if (event.target.checked) {
                console.log("Click Extra, box was checked, add this extra", extra);
                // Box was checked, add this extra
                this.extras.push(extra);
            } else {
                console.log("Click Extra, box was unchecked, remove this extra", this.extras);
                // TODO when event clicked "uncheck", remove this extra
            }
            // TODO after clicking this, rerun `selectDelvieryOption` because that saves the selected Option to session
            return true;
        },

        focusSelection: function (element) {
            // Apply blue focus to this element
            utility.applyAmongSiblings(element, ["monta-blue-focus"]);
        },
    }
});