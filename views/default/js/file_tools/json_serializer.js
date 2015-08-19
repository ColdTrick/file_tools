/**
 * Extend jQuery with a function to serialize to JSON
 */
define(function(require) {
	var $ = require('jquery');

	(function( $ ) {
		$.fn.serializeJSON = function() {
			var json = {};
			$.map($(this).serializeArray(), function(n, i) {
				if (json[n['name']]) {
					if (!json[n['name']].push) {
						json[n['name']] = [json[n['name']]];
					}
					json[n['name']].push(n['value'] || '');
				} else {
					json[n['name']] = n['value'] || '';
				}
			});
			return json;
		};
	})( jQuery );
});
