// Starting Prediction Library

var predictionioUX = {

	init : function (urls) {
		String.prototype.toCamelCase = function(cap1st) {
			return ((cap1st ? '-' : '') + this).replace(/-+([^-])/g, function(a, b) {
				return b.toUpperCase();
			});
		};

		this.recommendationURL = urls.content;
        this.baseproduct = urls.productid;
        this.eventUrl = urls.EventUrl;

		jQuery(document).ready(function () {
			var pageClasses = document.body.className.split(' ');
			pageClasses.some(function(pageClass) {
				if (pageClass.trim() !== '') {
					var camelizedPageClass = pageClass.toCamelCase();
					if (predictionioUX[camelizedPageClass]) {
						// ---- If the function exists, run it, otherwise, don't do anything. ---- //
						predictionioUX[camelizedPageClass]();
						return true;
					}
				}
			});
		});
    },

    sharedFunctions : {
        getUrlParams : function (url) {
            var params = {};
            var parser = document.createElement('a');
            parser.href = url;
            var query = parser.search.substring(1);
            var vars = query.split('&');
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split('=');
                params[pair[0]] = decodeURIComponent(pair[1]);
            }
            return params;
        },
    },

    cmsIndexIndex : function () {
		new Ajax.Request(
			this.recommendationURL,
			{
				method     : 'get',
				parameters : {
                    location : 'home'
                },
				onSuccess  : function(response) {
					var textContainer = $$('div.promo-home-content')[0];
					$(textContainer).insert(response.responseText);
				}
			});
	},

	catalogProductView: function() {
        if (this.baseproduct != '') {
            new Ajax.Request(
                this.recommendationURL,
                {
                    method     : 'get',
                    parameters : {
                        location : 'product',
                        product  : this.baseproduct
                    },
                    onSuccess  : function(response) {
                        var textContainer = $$('div.product-collateral')[0];
                        $(textContainer).insert({
                            before: response.responseText,
                        });
                    }
                }
            );

            var source = '';
            var currentParams = predictionioUX.sharedFunctions.getUrlParams(window.location.href);
            if (currentParams && currentParams.hasOwnProperty('cpmedium')) {
                if (currentParams.hasOwnProperty('cpsource')) {
                    source = currentParams.cpmedium;
                }

            }

            new Ajax.Request(
                this.eventUrl,
                {
                    method     : 'post',
                    parameters : {
                        source   : source,
                        product  : this.baseproduct,
                        event    : 'view',
                        location : 'product'
                    },
                    onSuccess  : function(response) {
                    }
                }
            );
        }
    },

	checkoutCartIndex: function() {
		new Ajax.Request(
			this.recommendationURL,
			{
				method     :'get',
				parameters : {
                    location : 'cart'
                },
				onSuccess  : function(response) {
					var textContainer = $$('div.col1-set.totals-shipping')[0];
					$(textContainer).insert(response.responseText);
				}
			});
	},

};

// End of Prediction Library
