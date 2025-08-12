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
            let retry = true;

            var orderData = new FormData();
            orderData.append('form_key', config.formKey);
            fetch(config.validateUrl, {
                method: 'POST',
                headers: {contentType: 'application/json'},
                body: orderData,
            }).then(response => response.json()).then(data => {
                buttonElement.querySelector('label').innerHTML = data.message;
                // Disable button to avoid spam after success
                retry = !data.success;
            })
                .catch(error => {
                    console.warn("Error", error);
                }).finally(() => {
                // Whatever happens, enable button again
                if (retry) {
                    buttonElement.disabled = false;
                }
            });
        }.bind(config);
    }
})
