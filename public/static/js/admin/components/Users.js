'use strict';

ViewComponent.Users = ViewComponent.register('Users', {
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
                user: $('#js-user-' + id).html(),
                email: $('#js-email-' + id).html(),
                company: $('#js-company-' + id).html(),
                role: $('#js-role-' + id).find(":selected").val()
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
        }
    },

    init: function (config) {
        this.on('render', function () {
            this.listObject = $('#js-usersList').DataTable({
                responsive: true,
                destroy: true
            });

            $('#js-usersList td a').tooltip({
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

    controller: 'users',
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