'use strict';

ViewComponent.Navigation = ViewComponent.register('Navigation', {

    actions: {
        load: function(e, controller, action) {
            this.parent.content.load(controller, action);
        }
    },

    init: function (config) {
        this.on('render', function () {
            $('#side-menu').metisMenu();
        }.bind(this));

        this.on('destroy', function () {
            //do something on destroy
        }.bind(this));
    },

    render: function () {
        var deferred = Q.defer();

        UI.requestJSON('/admin/navigation').then(function (response) {
            deferred.resolve(response.body);
        });

        return deferred.promise;
    }
});
