/**
 *
 */
define(function(require) {
	var elgg = require('elgg');
	var $ = require('jquery');
	var serializer = require('file_tools/json_serializer');
	var settings = require('file_tools/settings');

	/**
	 *
	 */
	var init = function() {
		$uploadifyButton = $('#uploadify-button-wrapper');

		if (!$uploadifyButton.length) {
			return;
		}

		$(document).on('click', '#file-tools-uploadify-cancel', cancel);
		$('#file-tools-multi-form').submit(upload);

		$uploadifyButton.uploadify({
			swf: elgg.normalize_url("mod/file_tools/vendors/uploadify/uploadify.swf"),
			uploader: elgg.normalize_url("mod/file_tools/procedures/upload/multi.php"),
			formData: {"X-Requested-With": "XMLHttpRequest"},
			buttonText: elgg.echo("file_tools:forms:browse"),
			queueID: "uploadify-queue-wrapper",
			fileTypeExts: settings.allowed_extensions,
			fileSizeLimit: settings.readable_file_size_limit,
			fileObjName: "upload",
			height: "23",
			width: "120",
			auto: false,
			onQueueComplete: function(queueData) {
				var folder = $('#file_tools_file_parent_guid').val();

				if (elgg.page_owner.type == "group") {
					var return_url = elgg.get_site_url + "file/group/" + elgg.page_owner.guid + "/all";
				} else {
					var return_url = elgg.get_site_url + "file/owner/" + elgg.page_owner.username;
				}

				var forward_location = return_url + "#";
				if (folder > 0) {
					forward_location += folder;
				}

				document.location.href = forward_location;
			},
			onUploadStart: function(file) {

				$('#uploadify-button-wrapper').uploadify("settings", "formData", $('#file-tools-multi-form').serializeJSON());
			},
			onUploadSuccess: function(file, data, response) {
				data = $.parseJSON(data);

				if (data && data.system_messages) {
					elgg.register_error(data.system_messages.error);
					elgg.system_message(data.system_messages.success);
				}
			},
			onUploadError: function(file, data, response) {
				data = $.parseJSON(data);

				if (data && data.system_messages) {
					elgg.register_error(data.system_messages.error);
					elgg.system_message(data.system_messages.success);
				}
			},
			onSelect: function(instance) {
			   $("#file-tools-uploadify-cancel").removeClass("hidden");
			},
			onClearQueue: function(queueItemCount) {
				$("#file-tools-uploadify-cancel").addClass("hidden");
			}
		});
	};

	/**
	 *
	 */
	var cancel = function() {
		$('#uploadify-button-wrapper').uploadify("cancel", "*");
	};

	/**
	 *
	 * @param {Object} event
	 */
	var upload = function(event) {
		$('#uploadify-button-wrapper').uploadify("upload", "*");

		return false;
	};
});



