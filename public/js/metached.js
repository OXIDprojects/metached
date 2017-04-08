if (typeof Object.assign !== 'function') {
    Object.assign = function(target, varArgs) { // .length of function is 2
        'use strict';
        if (target === null) { // TypeError if undefined or null
            throw new TypeError('Cannot convert undefined or null to object');
        }

        var to = Object(target);

        for (var index = 1; index < arguments.length; index++) {
            var nextSource = arguments[index];

            if (nextSource !== null) { // Skip over if undefined or null
                for (var nextKey in nextSource) {
                    // Avoid bugs when hasOwnProperty is shadowed
                    if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                        to[nextKey] = nextSource[nextKey];
                    }
                }
            }
        }
        return to;
    };
}

var Metached = (function () {
    var storage = {};
    var uid = 0;

    function Metached(baseUrl) {
        var that = this;

        storage[this.id = uid++] = {
            _response: {success: false, message: ''},
            _ajax: new XMLHttpRequest(),
            _url: baseUrl
        };

        Object.defineProperty(this, 'response', {
            get: function () {
                return storage[that.id]._response;
            }
        });

        Object.defineProperty(this, 'xhr', {
            get: function () {
                return storage[that.id]._ajax;
            }
        });

        Object.defineProperty(this, 'url', {
            get: function () {
                return storage[that.id]._url;
            }
        });

        this.xhr.onreadystatechange = function () {
            if (that.xhr.readyState === XMLHttpRequest.DONE) {
                try {
                    var success = that.xhr.getResponseHeader('X-Status'),
                        message = that.xhr.getResponseHeader('X-Status-Message');
                    storage[that.id]._response = {
                        success: (success === '1'),
                        message: message
                    };

                    storage[that.id]._response.data = JSON.parse(that.xhr.responseText);
                } catch (e) {
                    storage[that.id]._response = {
                        success: false,
                        message: 'Unknown server error.\n' + e.message
                    };
                }

                that.emit('request.finished', that.response);
            }
        };
    }

    Emitter(Metached.prototype);

    Metached.prototype.save = function (oxidClass, data) {
        var requestData = Object.assign({}, {oxidClass: oxidClass}, data);
        this.xhr.open('POST', this.url, true);
        this.xhr.send(JSON.stringify(requestData));
    };

    Metached.prototype.createSortables = function (sortables) {
        var that = this;

        for (var i = 0; i < sortables.length; i++) {
            Sortable.create(sortables[i], {
                animation: 150,
                dataIdAttr: 'data-module-class',
                group: sortables[i].dataset.extendedClass,
                store: {
                    /**
                     * Get the order of elements. Called once during initialization.
                     * @param   {Sortable}  sortable
                     * @returns {Array}
                     */
                    get: function (sortable) {
                        return [];
                    },

                    /**
                     * Save the order of elements. Called onEnd (when the item is dropped).
                     * @param {Sortable}  sortable
                     */
                    set: function (sortable) {
                        var order = sortable.toArray();

                        that.save(sortable.options.group.name, {sorting: order});
                    }
                }
            });
        }
    };

    return Metached;
})();
