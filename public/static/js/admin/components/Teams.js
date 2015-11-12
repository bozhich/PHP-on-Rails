'use strict';

ViewComponent.Teams = ViewComponent.register('Teams', {
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
                name: $('#js-name-' + id).html(),
                coef: $('#js-coef-' + id).html(),
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
            this.listObject = $('#js-teamsList').DataTable({
                responsive: true,
                destroy: true
            });

            $('#js-teamsList td a').tooltip({
                selector: "[data-toggle=tooltip]",
                container: "body"
            });


            var searchFilterValues = [];
            this.competitionsList.forEach(function (el) {
                searchFilterValues.push(el.name);
            });

            $('#js-teamsList').dataTable().columnFilter({
                aoColumns: [
                    null,
                    null,
                    {
                        type: "select",
                        values: searchFilterValues
                    },
                    null,
                    null,
                    null,
                ]
            });


        }.bind(this));

        this.on('parsed', function () {
        }.bind(this));

        this.on('destroy', function () {
            //do something on destroy\
            this.listObject.destroy();
        }.bind(this));
    },

    controller: 'teams',
    action: 'list',
    id: null,
    listObject: null,
    competitionsList: null,

    render: function () {
        var data = {};
        var deferred = Q.defer();

        var url = '/admin/' + this.controller + '/' + this.action;
        if (this.id > 0) {
            url += '/' + this.id;
        }
        UI.requestJSON(url).then(function (response) {
            deferred.resolve(response.body);
            this.competitionsList = response.data;
        }.bind(this));

        return deferred.promise;
    }
});