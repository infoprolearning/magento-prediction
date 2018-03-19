// Starting Prediction Library

var predictionioUX = {

	init: function (urls) {
		String.prototype.toCamelCase = function(cap1st) {
			return ((cap1st ? '-' : '') + this).replace(/-+([^-])/g, function(a, b) {
				return b.toUpperCase();
			});
		};

		this.recommendationURL = urls.content;
		this.baseproduct = urls.productid;

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


	cmsIndexIndex: function () {
		new Ajax.Request(
			this.recommendationURL,
			{
				method:'get',
				parameters: {location:'home'},
				onSuccess: function(response) {
					var textContainer = $$('div.promo-home-content')[0];
					$(textContainer).insert(response.responseText);
				}
			});
	},

	catalogProductView: function() {
		new Ajax.Request(
			this.recommendationURL,
			{
				method:'get',
				parameters: {location:'product', product:this.baseproduct},
				onSuccess: function(response) {
					var textContainer = $$('div.product-collateral')[0];
					$(textContainer).insert({
						before: response.responseText,
						});
				}
			});
	},

	checkoutCartIndex: function() {
		new Ajax.Request(
			this.recommendationURL,
			{
				method:'get',
				parameters: {location:'cart'},
				onSuccess: function(response) {
					var textContainer = $$('div.col1-set.totals-shipping')[0];
					$(textContainer).insert(response.responseText);
				}
			});
	},

};

// End of Prediction Library
