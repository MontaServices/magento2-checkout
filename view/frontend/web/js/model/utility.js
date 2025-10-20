define([], function () {
    'use strict';

    return {
        /**
         * Select the first item in the list by clicking
         *
         * @param element
         */
        selectFirst: function (element) {
            // use timeout(zero) to wait for next click when all elements are rendered
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
            this.applyAmongSiblings(event.currentTarget, ['primary']);
        },

        /** Apply class to element, remove that class from siblins
         *
         * @param element
         * @param targetClasses - string[]
         */
        applyAmongSiblings: function (element, targetClasses) {
            if (element && element.classList) {
                let self = this;
                if (Array.isArray(element)) {
                    element.forEach(function (subelement) {
                        self.applyAmongSiblings(subelement, targetClasses);
                    })
                } else {
                    let parent = element.parentElement;

                    // Remove focus from all sibling tiles
                    if (parent) {
                        parent.childNodes.forEach(function (sibling) {
                            // Only same-tag siblings with a classlist
                            if (sibling.classList && sibling.tagName === element.tagName) {
                                sibling.classList.remove.apply(sibling.classList, targetClasses);
                            }
                        });
                    }

                    // Add selection classes to the clicked tile
                    element.classList?.add.apply(element.classList, targetClasses);
                }
            } else {
                console.warn("Cannot apply classes since no element was passed.");
            }
        },
    };
});