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
            this.updateQuote(quote, option);

            // Return TRUE to propagate default click behavior
            return true;
        },

        clickExtra: function (extra, event, quote) {
            // When checkbox was checked
            // TODO click now runs twice, on <label> and on <input>. Second one is the real one so that just works
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

        /**
         * Update quote with selected option
         * @param quote
         * @param option
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
                // Save the selected Option object to address
                // TODO add this.extras to option object, first check if they belong to this option
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