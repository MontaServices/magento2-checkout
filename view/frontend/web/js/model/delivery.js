define([], function () {
    'use strict';

    return {
        isSelected: function () {
            // in this context, $this is still the component
        },

        selectDay: function (option, event) {
            if (event) {
                let selectedTileStyle = "bg-blue-50 border-blue-400";
                // TODO style this tile as selected (same style as hover); option.classes does not update in UI
                // option.additionalClasses = selectedTileStyle;
                // event.target.addClass(selectedTileStyle);
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
        }
    }
});