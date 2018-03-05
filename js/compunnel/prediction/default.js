// Starting Prediction Library

var predictionioUX = {

	init: function () {
		String.prototype.toCamelCase = function(cap1st) {
			return ((cap1st ? '-' : '') + this).replace(/-+([^-])/g, function(a, b) {
				return b.toUpperCase();
			});
		};

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
		console.log('Home Page');
	},
};

predictionioUX.init();

// End of Prediction Library
