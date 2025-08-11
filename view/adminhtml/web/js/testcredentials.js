define(['underscore'], function (_) {
    'use strict';
    
    /**
     * @param {Object} config
     * @param {Element} element - the Button element
     * @returns {void}
     */
    return function (config, element) {
        element.onclick = null;
        console.log("init", config, element);

        element.onclick = function (event) {
            console.log("Button clicked", event);
        }
    }
})
