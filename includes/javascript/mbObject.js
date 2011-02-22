var MbObject = {
	edit: function(object, options) {
		var object_guid = object;
		
		if (Object.isElement(object)) {
			object_guid = object.getAttribute("data-object_guid");
		}
		
    options = Object.extend({
      target: "object-editor",
      customValues: null
    }, options);
		
		var url = new Url("system", "ajax_edit_object");
		
		if (options.customValues) {
			url.addObjectParam("_v", options.customValues);
		}
		
		if (object_guid) {
			url.addParam("object_guid", object_guid);
		}
		url.requestUpdate(options.target);
	},
  editCallback: function(id, obj) {
    MbObject.list(obj._class_name);
    MbObject.edit(obj._guid);
  },
	list: function(object_class, columns) {
	  var url = new Url("system", "ajax_object_tag_tree");
	  url.addParam("object_class", object_class);
	  url.addParam("col[]", columns);
	  url.requestUpdate("tag-tree");
	},
  viewBackRefs : function(object_class, object_ids) {
    object_ids = object_ids instanceof Array ? object_ids : [object_ids];
    var url = new Url("system", "view_back_refs");
    url.addParam("object_class", object_class);
    url.addParam("object_ids[]", object_ids);
    url.popup(300 * object_ids.length + 200, 600, "View back refs");
  }
};
