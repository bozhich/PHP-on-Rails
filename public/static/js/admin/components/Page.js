'use strict';

ViewComponent.Page = ViewComponent.register('Page', {
    navigation: null,
    content: null,

    init: function (config) {
        this.on('render', function () {
            $('#side-menu').metisMenu();

            $(window).bind("load resize", function() {
                var topOffset = 50;
                var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
                if (width < 768) {
                    $('div.navbar-collapse').addClass('collapse');
                    topOffset = 100; // 2-row-menu
                } else {
                    $('div.navbar-collapse').removeClass('collapse');
                }

                var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
                height = height - topOffset;
                if (height < 1) height = 1;
                if (height > topOffset) {
                    $("#page-wrapper").css("min-height", (height) + "px");
                }
            });
        }.bind(this));

        this.on('parsed', function () {
            this.navigation = this.find('Navigation')[0];
            this.content = this.find('Contents')[0];
        }.bind(this));

        this.on('destroy', function () {
            //do something on destroy
        }.bind(this));

    },

    render: function () {
        return '<Navigation></Navigation><contents></contents>';
    }
});
