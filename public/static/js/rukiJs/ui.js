var UI = (function () {
    'use strict';

    var _requestsQue = {};
    var _requestIsScheduled = false;
    var _requestCounter = 0;


    function _addRequest(url, data) {
        var deferred = Q.defer();
        data = data || {};
        if (!_requestIsScheduled) {
            setTimeout(_makeMultipleRequests, 10);
            _requestIsScheduled = true;
        }

        _requestsQue['request_' + _requestCounter] = {
            url: url,
            data: data,
            sent: false,
            deferred: deferred
        };
        _requestCounter++;

        return deferred.promise;

    }


    function _makeMultipleRequests() {
        var deferred = Q.defer();
        var data = {};

        _requestIsScheduled = false;

        Object.keys(_requestsQue).forEach(function (id) {
            if (!_requestsQue[id].sent) {
                data[id] = {
                    url: _requestsQue[id].url,
                    data: _requestsQue[id].data
                };
                _requestsQue[id].sent = true;
            }
        });


        _makeRequest('/multipleRequest.php', data, true).then(function (response) {
            Object.keys(response).forEach(function (id) {
                _requestsQue[id].deferred.resolve(response[id]);
                delete _requestsQue[id];
            });
        }).fail(function (err) {
            console.log(err);
        });


        return deferred.promise;
    }


    function _makeRequest(url, data, parseAsJson) {
        var deferred = Q.defer();
        var config = {
            url: url,
            data: data || {},
            type: "POST",
            complete: function (response) {
                var jsonResponse;

                if (response.getResponseHeader('Server-redirect')) {
                    window.location.href = response.getResponseHeader('Server-redirect');
                    return;
                }


                if (parseAsJson) {
                    try {
                        jsonResponse = jQuery.parseJSON(response.responseText);
                        if (typeof jsonResponse.redirect !== 'undefined' && jsonResponse.redirect) {
                            window.location.href = jsonResponse.redirect;
                        }
                        if (typeof jsonResponse.translates !== 'undefined' && jsonResponse.translates) {
                            // @todo KW.Dialog.push(jsonResponse.translates);
                        }
                        deferred.resolve(jsonResponse);
                    } catch (error) {
                        deferred.reject(error);
                    }


                } else {
                    deferred.resolve(response.responseText);
                }

            },
            error: function (req, e) {
                if (req.getResponseHeader('Server-redirect')) {
                    window.location.href = req.getResponseHeader('Server-redirect');
                }
                deferred.reject(e);
            }

        };
        if (parseAsJson) {
            config.dataType = "json";
        }
        jQuery.ajax(config);

        return deferred.promise;
    }


    function _request(url, data, bufferRequest) {
        bufferRequest = bufferRequest === undefined ? true : false;
        return bufferRequest ? _addRequest(url, data) : _makeRequest(url, data, false);
    }


    function _requestJSON(url, data, bufferRequest) {
        //bufferRequest = bufferRequest === undefined ? true : false;
        //return bufferRequest ? _addRequest(url, data) : _makeRequest(url, data, true);
        return _makeRequest(url, data, true);
    }

    function isMobileBrowser() {
        if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
        //	|| navigator.userAgent.match(/Macintosh/i)
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    // Assign methods to the public scope
    return {
        request: _request,
        requestJSON: _requestJSON,
        isMobileBrowser: isMobileBrowser
    };
})();




