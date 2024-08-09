define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return {
        /**
         * Validate Kbank response
         *
         * @param {Object} context
         * @returns {jQuery.Deferred}
         */
        validate: function (context) {
            var state = $.Deferred(),
                messages = [];

            if (context.hasOwnProperty('token')) {
                state.resolve();
            } else {
                messages.push($t('There is an error during getting token.'));
                state.reject(messages);
            }

            return state.promise();
        }
    };
});

