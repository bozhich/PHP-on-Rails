'use strict';

ViewComponent.Contents = ViewComponent.register('Contents', {

    actions: {
        selectAll: function (e, elClass) {
            $('.' + elClass).prop('checked', true);
        },
        selectNone: function (e, elClass) {
            $('.' + elClass).prop('checked', false);
        },

        login: function () {
            var data = {
                user: $('#js-user').val(),
                pass: $('#js-pass').val()
            };

            UI.requestJSON(this.renderModule + '/' + 'session', data).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                this.parent.rerender();
            }.bind(this));
        },
        load: function (e, controller, action, id) {
            if (typeof (e) == 'function') {
                this.load(controller, action, id);
            } else {
                this.load(e, controller, action);
            }

        },

        reload: function () {
            this.rerender();
        },

        translateScan: function () {
            Pace.restart();
            $('.js-scan').removeClass('btn-success').addClass('btn-default');

            var data = {};
            var url = '/admin/remoteTranslate/scan/';
            UI.requestJSON(url, data).then(function (response) {
                $('#js-scan').removeClass('btn-default').addClass('btn-success');
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);
                this.rerender();

            }.bind(this));
        },

        addUser: function () {
            var data = {
                user: $('#js-user').val(),
                email: $('#js-email').val(),
                pass: $('#js-pass').val(),
                role: $('#js-role').find(":selected").val(),
                company: $('#js-company').val()
            };
            var url = '/admin/users/create/';
            UI.requestJSON(url, data).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);

                this.renderController = 'users';
                this.renderAction = 'index';
                this.rerender();
            }.bind(this));
        },

        addRole: function () {
            var data = {
                name: $('#js-name').val(),
                desc: $('#js-desc').val(),
                perm: []
            };
            $('.permCheckbox').each(function (k, el) {
                if ($(el).is(':checked')) {
                    data['perm'].push($(el).val());
                }
            });

            var url = '/admin/roles/create/';
            UI.requestJSON(url, data).then(function (response) {
                if (!response.status) {
                    UI.Dialog.error(response.body);
                    return;
                }
                UI.Dialog.info(response.body);

                this.renderController = 'roles';
                this.renderAction = 'index';
                this.rerender();
            }.bind(this));
        }
    },


    renderModule: 'admin',
    renderController: 'index',
    renderAction: 'load',
    renderId: 0,
    loadHash: false,


    init: function (config) {
        this.on('render', function () {
            $('#side-menu').metisMenu();
        }.bind(this));


        var url = location.hash.substr(1).split('/');
        url.list('controller', 'action', 'id');
        if (controller.length > 0) {
            if (controller != this.renderController && action != this.renderAction && id != this.renderId) {
                this.loadHash = true;
                this.renderController = controller;
                this.renderAction = action;
                this.renderId = id;
            }
        }

        this.on('destroy', function () {
            //do something on destroy
        }.bind(this));
    },

    render: function () {
        var deferred = Q.defer();

        var url = this.renderModule + '/' + this.renderController + '/' + this.renderAction;
        if (this.renderId > 0) {
            url += '/' + this.renderId;
        }
        UI.requestJSON(url).then(function (response) {
            deferred.resolve(response.body);
        });

        return deferred.promise;
    },

    load: function (controller, action, id) {
        Pace.restart();
        this.renderController = controller;
        this.renderAction = action;
        this.renderId = id;

        var location_hash = '#' + controller;
        if (action == null) {
            location_hash += '/index';
        } else {
            location_hash += '/' + action;
        }

        if (typeof id != "undefined" && id != null) {
            location_hash += '/' + id;
        }
        window.location.hash = location_hash;
        this.rerender();
    },

    reloadNavigation: function () {
        this.parent.navigation.rerender();
    }
});
