define([], function () {
    'use strict';
    // Selected state
    let selectedTileStyle = ["monta-blue-focus"];

    return {
        isSelected: function () {
            // in this context, $this is still the component
        },

        /** Mark the clicked day as select and unselect siblins
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
            let self = this;
            if (Array.isArray(element)) {
                element.forEach(function (subelement) {
                    self.focusSelection(subelement);
                })
            } else {
                let parent = element.parentElement;

                // Remove focus from all sibling tiles
                if (parent) {
                    console.log("has parent, find sibling, apply");
                    parent.childNodes.forEach(function (el) {
                        // Only the same tag siblins with a classlist
                        if (el.classList && el.tagName === element.tagName) {
                            el.classList.remove.apply(el.classList, selectedTileStyle);
                        }
                    });
                }

                // Add selection classes to the clicked tile
                element.classList?.add.apply(element.classList, selectedTileStyle);
            }
        },
    }
});