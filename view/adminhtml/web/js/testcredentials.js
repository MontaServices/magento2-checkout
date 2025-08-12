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

            console.log("hello, call url", config.validateUrl);

            let params = {
                form_key: config.formKey
            };
            var orderData = new FormData();

            orderData.append('form_key', config.formKey);
            console.log("Call with formdata: ", params);
            fetch(config.validateUrl, {
                method: 'POST',
                headers: {contentType: 'application/json'},
                body: orderData,
            }).then(response => {
                console.log("Got response: ", response.json());
                if (response.redirected && response.url.includes("review")) {
                    throw new Error();
                }
                return response.text();
            }).catch(error => {
                console.warn("Error", error);
            });
        }.bind(config);
    }
})
