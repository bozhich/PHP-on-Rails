'use strict';

ViewComponent.Roles = ViewComponent.register('Roles', {
    actions: {
        delete: function (e, id, confirmed) {
            var url = '/admin/' + this.controller + '/delete/' + id;
            var data = {};

            if (confirmed) {
                data['confirmed'] = confirmed;
            }
            UI.requestJSON(url, data).then(function (response) {
                var status = +response.status;
                if (status === 1) {
                    UI.Dialog.info(response.body);
                    this.rerender();
                } else if (status === 2) {
                    UI.Dialog.confirm(response.body, function (result) {
                        if (result) {
                            this.callAction('delete', e, id, 1);
                        }
                    }.bind(this));
                } else {
                    UI.Dialog.error(response.body);
                }
            }.bind(this));
        },

        save: function (e, id) {
            var data = {
                name: $('#js-name-' + id).html(),
                desc: $('#js-desc-' + id).html()
            };

            var url = '/admin/' + this.controller + '/save/' + id;
            UI.requestJSON(url, data).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);
                this.rerender();

            }.bind(this));
        },

        permissions: function (e, id) {
            var url = '/admin/' + this.controller + '/permissions/' + id;
            UI.requestJSON(url, {}).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }


                var html = '';
                Object.keys(JSON.parse(response.data)).forEach(function (permName) {
                    html += permName + '<br />';
                });
                UI.Dialog.info(html);
            }.bind(this));
        }
    },

    init: function (config) {
        this.on('render', function () {
            this.listObject = $('#js-rolesList').DataTable({
                responsive: true,
                destroy: true
            });

            $('#js-rolesList td a').tooltip({
                selector: "[data-toggle=tooltip]",
                container: "body"
            });

        }.bind(this));

        this.on('parsed', function () {
        }.bind(this));

        this.on('destroy', function () {
            //do something on destroy\
            this.listObject.destroy();
        }.bind(this));
    },

    controller: 'roles',
    action: 'list',
    id: null,
    listObject: null,

    render: function () {
        var data = {};

        var deferred = Q.defer();

        var url = '/admin/' + this.controller + '/' + this.action;
        if (this.id > 0) {
            url += '/' + this.id;
        }
        UI.requestJSON(url).then(function (response) {
            deferred.resolve(response.body);
        });

        return deferred.promise;
    }
});