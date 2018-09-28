var ModalWindow = (function (window, document, undefined, $) {

	return function (config) {

		var __config = config;
		var __$containerForms = null;

		var that = {

			init: function () {
				__$containerForms = $('.container-blackout-popup-window');

				that.renderForm();

			},
			renderForm: function () {
				var form = that.getForm();

				__$containerForms.html(form);
				(config.renderBodyCallback) ? config.renderBodyCallback.call(this, __$containerForms, config) : '';
				that.onCloseForm(__$containerForms);
				__$containerForms.show();

			},
			getForm: function () {
				var form = '';

				$.ajax({
					type: "GET",
					url: config.actionUrl,
					async: false
				}).done(function (data) {

					if (data) {
						form = data;
					}

				}).fail(function (jqXHR, ajaxOptions, thrownError) {
					/* location.reload()*/
				});

				return form;

			},

			onCloseForm: function ($form) {
				$form.off('click', config.closeBtn).on('click', config.closeBtn, function () {
					that.closeForm();
				})
			},

			closeForm: function () {
				if (__$containerForms) {
					__$containerForms.hide();
				}
			}

		};

		return that;
	}

}(window, document, undefined, jQuery));

