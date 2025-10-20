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

        /** Select a mode for the checkout
         *
         * @param mode - string
         * @param event
         */
        clickMode: function (mode, event) {
            // Find element for this mode
            // TODO replace all this HTML logic with a observable 'mode' property on component
            let element = document.querySelector(".tab-" + mode);
            if (element) {
                // Remove 'hidden' class from this one, add to siblings
                this.applyAmongSiblings(element, ['hidden'], false);
                // Add 'primary' class to this button element, remove from siblings
                this.applyAmongSiblings(event.currentTarget, ['primary']);
            } else {
                console.warn("Cannot find element for mode: " + mode);
            }
        },

        /** Apply class to element, remove that class from siblins
         *
         * @param element
         * @param targetClasses - string[]
         * @param add - Pass FALSE to remove classes instead
         */
        applyAmongSiblings: function (element, targetClasses, add = true) {
            if (element && element.classList) {
                let self = this;
                if (Array.isArray(element)) {
                    element.forEach(function (subelement) {
                        self.applyAmongSiblings(subelement, targetClasses, add);
                    })
                } else {
                    let parent = element.parentElement;

                    // Remove focus from all sibling tiles
                    if (parent) {
                        parent.childNodes.forEach(function (sibling) {
                            // Only same-tag siblings with a classlist
                            if (sibling.classList && sibling.tagName === element.tagName) {
                                if (add) {
                                    sibling.classList.remove.apply(sibling.classList, targetClasses);
                                } else {
                                    sibling.classList.add.apply(sibling.classList, targetClasses);
                                }
                            }
                        });
                    }

                    // Add selection classes to the clicked tile
                    if (add) {
                        element.classList?.add.apply(element.classList, targetClasses);
                    } else {
                        element.classList?.remove.apply(element.classList, targetClasses);
                    }
                }
            } else {
                console.warn("Cannot apply classes since no element was passed.");
            }
        },
    };
});