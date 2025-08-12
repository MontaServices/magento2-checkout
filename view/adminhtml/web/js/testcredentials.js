define(['underscore'], function (_) {
    'use strict';

    /**
     * @param {Object} config
     * @param {Element} element - the Button element
     * @returns {void}
     */
    return function (config, buttonElement) {
        buttonElement.onclick = function (event) {
            // Avoid double click
            buttonElement.disabled = true;

            fetch('/monta/credentials/validate', {
                method: 'POST',
                headers: {contentType: 'application/json'}
            }).then(response => {
                console.log("Got response: ", response.json());
                if (response.redirected && response.url.includes("review")) {
                    throw new Error();
                }
                return response.text();
            }).catch(error => {
                console.warn("Error", error);
            });
        }
    }
})
