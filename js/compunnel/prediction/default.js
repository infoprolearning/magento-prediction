// Starting Prediction Library

var predictionioUX = {

	init: function (urls) {
		String.prototype.toCamelCase = function(cap1st) {
			return ((cap1st ? '-' : '') + this).replace(/-+([^-])/g, function(a, b) {
				return b.toUpperCase();
			});
		};

		this.recommendationURL = urls.content;

		jQuery(document).ready(function () {
			var pageClasses = document.body.className.split(' ');
			pageClasses.forEach(function(pageClass) {
				if (pageClass.trim() !== '') {
					var camelizedPageClass = pageClass.toCamelCase();
					if (predictionioUX[camelizedPageClass]) {
						// ---- If the function exists, run it, otherwise, don't do anything. ---- //
						predictionioUX[camelizedPageClass]();
					}
				}
			});
		});
	},


	cmsIndexIndex: function () {
		console.log(this.recommendationURL);
	},
};

// End of Prediction Library
