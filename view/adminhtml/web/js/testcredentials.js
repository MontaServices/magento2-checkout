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
            // Notify user API is calling
            updateButton('Checking ⏳');
            let retry = true;

            let orderData = new FormData();
            orderData.append('form_key', config.formKey);
            fetch(config.validateUrl, {
                method: 'POST',
                headers: {contentType: 'application/json'},
                body: orderData,
            }).then(response => response.json()).then(data => {
                updateButton(data.message);
                // Disable button to avoid spam after success
                retry = !data.success;
            })
                .catch(error => {
                    console.warn("Error", error);
                    updateButton(error.message);
                }).finally(() => {
                // Whatever happens, enable button again
                if (retry) {
                    buttonElement.disabled = false;
                }
            });

            /** Update button label
             *
             * @param text
             */
            function updateButton(text) {
                buttonElement.querySelector('label').innerHTML = text;
            }
        }.bind(config);
    }
})