'use strict';

ViewComponent.Members = ViewComponent.register('Members', {
    actions: {
        delete: function (e, id) {
            var url = '/admin/' + this.controller + '/delete/' + id;
            UI.requestJSON(url).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);
                this.rerender();
            }.bind(this));
        },

        save: function (e, id) {
            var data = {
                user: $('#js-user-' + id).html(),
                email: $('#js-email-' + id).html()
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
            this.listObject = $('#js-membersList').DataTable({
                responsive: true,
                destroy: true
            });

            $('#js-membersList td a').tooltip({
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

    controller: 'members',
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