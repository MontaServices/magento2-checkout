/**
 * @kudos Darren Felton <https://magento.stackexchange.com/a/293919>
 */
define([
    'mage/utils/wrapper',
], function (wrapper) {
    'use strict';

    return function (payloadExtender) {
        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            payload = originalAction(payload);

            console.log("Extending payload", payload);
            // Set extension attribute in Shipping address
            payload.addressInformation.shipping_address.extension_attributes = {
                // TODO obviously get the real data from quote
                SelectedDeliveryOption: "PostNL_20250925_202509260815_202509261045",
            };
            return payload;
        });
    };
});