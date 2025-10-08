define([], function () {
    'use strict';
    // Selected state
    let selectedTileStyle = ["monta-blue-focus"];

    return {
        isSelected: function () {
            // in this context, $this is still the component
        },

        // Mark the clicked day as selected and unselect siblings
        selectDay: function (option, event) {
            if (event && event.currentTarget) {
                let target = event.currentTarget;
                let parent = target.parentElement;

                // Remove focus from all sibling tiles
                if (parent) {
                    parent.childNodes.forEach(function (el) {
                        if (el.classList && el.tagName === target.tagName) {
                            el.classList.remove.apply(el.classList, selectedTileStyle);
                        }
                    });
                }

                // Add selection classes to the clicked tile
                target.classList.add.apply(target.classList, selectedTileStyle);
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