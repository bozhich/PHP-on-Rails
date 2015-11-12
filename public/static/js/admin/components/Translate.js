'use strict';
ViewComponent.Translate = ViewComponent.register('Translate', {
    actions: {
        save: function (e, id) {
            var data = {
                value: $('#js-value-' + id).html()
            };

            var url = '/admin/' + this.controller + '/save/' +  id;
            UI.requestJSON(url, data).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);
                this.parent.reloadNavigation();
               // this.rerender();

            }.bind(this));
        }
    },

    init: function (config) {
        this.on('render', function () {
            this.listObject = $('#js-translateList').DataTable({
                responsive: true,
                "pageLength": 25,
                destroy: true
            });
        }.bind(this));

        this.on('parsed', function () {
        }.bind(this));

        this.on('destroy', function () {
            //do something on destroy\
            this.listObject.destroy();
        }.bind(this));
    },

    controller: 'translate',
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
