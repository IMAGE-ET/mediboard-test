var MbObject = {
	edit: function(object, options) {
		var object_guid = object;
		
		if (Object.isElement(object)) {
			object_guid = object.getAttribute("data-object_guid");
		}
		
    options = Object.extend({
      target: "object-editor"
    }, options);
		
		var url = new Url("system", "ajax_edit_object");
		if (object_guid) {
			url.addParam("object_guid", object_guid);
		}
		url.requestUpdate(options.target);
	},
	editCallback: function(id, obj) {
		MbObject.edit(obj._guid);
	}
}
