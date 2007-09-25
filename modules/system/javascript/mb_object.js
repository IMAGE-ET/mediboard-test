// $Id: $

var MbObject = {
  viewBackRefs : function(object_class, object_ids) {
    object_ids = object_ids instanceof Array ? object_ids : [object_ids];

    var url = new Url;
    url.setModuleAction("system", "view_back_refs");
    url.addParam("object_class", object_class);
    url.addParam("object_ids[]", object_ids);
    url.popup(300 * object_ids.length + 100, 600, "View back refs");
  }
}