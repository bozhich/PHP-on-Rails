'use strict';

ViewComponent.UIButton = ViewComponent.register('UIButton', {


    init: function (config) {
        this.classNames = ['btn btn-block'],
            this.action = function () {
            };
        this.caption = '';
        this.timeout = 2000;
        this.disable = true;
        this.disabled = false;
        this.id = '';
        this.button = null;
        //this.span = null;


        if (config.action) {
            this.action = config.action;
        }
        if (config.caption) {
            this.caption = config.caption;
        }
        if (config.timeout) {
            this.timeout = config.timeout;
        }
        if (config.disabable !== undefined) {
            this.disabable = config.disabable;
        }
        if (config.id !== undefined) {
            this.id = config.id;
        }

        this.id = config.id || '';

        this.classNames = ['btn btn-block'];
        if (config.classnames) {
            if (typeof config.classnames === 'string') {
                config.classnames = config.classnames.split(',');
            }
            config.classnames.forEach(function (className) {
                this.classNames.push(className);
            }.bind(this));
        }


    },


    render: function () {
        var self = this;
        this.button = document.createElement('button');
        this.button.className = this.classNames.join(" ");
        this.button.value = this.caption;
        this.button.innerHTML = this.caption;
        this.button.type = 'button';

        $(this.button).click(function () {
            if (self.disabled) {
                return false;
            }

            self.action();
            if (self.disable) {
                self.setDisabled(true);
                setTimeout(function () {
                    self.setDisabled(false);
                }, self.timeout);
            }
        });
        return this.button;
    },


    style: function (conf) {
        $(this.button).css(conf);
    },

    addClass: function (className) {
        $(this.button).addClass(className);
    },

    removeClass: function (className) {
        $(this.button).removeClass(className);
    },

    setCaption: function (caption) {
        this.caption = caption;
        //this.span.innerHTML = caption;
        this.button.innerHTML = caption;
    },

    setDisabled: function (flag) {
        this.disabled = flag;
        if (flag) {
            $(this.button).addClass('inactive');
        } else {
            $(this.button).removeClass('inactive');
        }
    }


});