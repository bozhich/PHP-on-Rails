'use strict';


if (!Array.prototype.forEach) {
	Array.prototype.forEach = function (callback) {
		var i = 0;
		for (i = 0; i < this.length; i++) {
			callback(this[i], i);
		}
	};
}



if (!Function.prototype.bind) {
	Function.prototype.bind = function (oThis) {

		var aArgs = Array.prototype.slice.call(arguments, 1),
		fToBind = this,
		fNOP = function () {},
		fBound = function () {
			return fToBind.apply(this instanceof fNOP ? this : oThis, aArgs.concat(Array.prototype.slice.call(arguments)));
		};

		fNOP.prototype = this.prototype;
		fBound.prototype = new fNOP();

		return fBound;
	};
}


if(!Object.keys){
	Object.keys = function(object){
		var i, result = [];
		for(i in object){
			if(object.hasOwnProperty(i)){
				result.push(i);
			}
		}
		return result;
	};
}

if (!window.console) {
	window.console = {
		log: function() { return ''; },
		warn: function() { return ''; },
		info: function() { return ''; }
	}
}

Array.prototype.list = function()
{
    var
        limit = this.length,
        orphans = arguments.length - limit,
        scope = orphans > 0  && typeof(arguments[arguments.length-1]) != "string" ? arguments[arguments.length-1] : window
        ;

    while(limit--) scope[arguments[limit]] = this[limit];

    if(scope != window) orphans--;

    if(orphans > 0)
    {
        orphans += this.length;
        while(orphans-- > this.length) scope[arguments[orphans]] = null;
    }
}
