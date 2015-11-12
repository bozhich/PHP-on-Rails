'use strict';

ViewComponent.EventListener = ViewComponent.register('EventListener', {


    init: function (conf) {
        this.eventsMap = {
            click: false,
            mouseover: false,
            mousedown: false,
            mouseup: false,
            keyup: false,
            keydown: false,
            keypress: false,
            change: false
        };

        Object.keys(this.eventsMap).forEach(function (ev) {
            if (conf[ev] && typeof conf[ev] === 'function') {
                this.eventsMap[ev] = conf[ev];
            }
        }.bind(this));


        this.on('render', function () {
            var target = null, i;

            if (this.renderTree && this.renderTree.length) {
                for (i = 0; i < this.renderTree.length; i++) {
                    if (this.renderTree[i].nodeType !== 3) {
                        target = this.renderTree[i];
                        break;
                    }
                }
                if (!target) {
                    return;
                }

                $(target).off();
                Object.keys(this.eventsMap).forEach(function (ev) {
                    if (typeof this.eventsMap[ev] === 'function') {
                        $(target).on(ev, function (e) {
                            this.eventsMap[ev](e.originalEvent);
                        }.bind(this));
                    }
                }.bind(this));
            }
        }.bind(this));
    }
});
