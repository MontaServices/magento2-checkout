define([], function () {
    'use strict';
    return {
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

            return true;
        },

        /**
         *  Select a mode for the checkout
         * @param mode
         * @param event
         */
        clickMode: function (mode, event) {
            let element = event.currentTarget;
            if (element.classList) {

                element.classList.add.apply(element.classList, ['primary']);
                element.classList.remove.apply(element.classList, ['secondary']);
            }
            // TODO also remove class from siblings
        },
    };
});