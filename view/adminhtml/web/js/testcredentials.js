define(['underscore'], function (_) {
    'use strict';

    return {
        events: {
            onclick: function (event) {
                console.log("Button clicked", event);
            }
        },

        init: function (buttonElement) {
            buttonElement.onclick = this.events.onclick;
        }
    }
})
