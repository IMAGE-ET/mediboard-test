var ExObject = {
  container: null,
  register: function(container, options) {
    this.container = $(container);
    
    options = Object.extend({
      ex_class_id: null,
      object_guid: null,
      event: null
    }, options);
    
    var url = new Url("system", "ajax_widget_ex_classes");
    url.addParam("object_guid", options.object_guid);
    url.addParam("ex_class_id", options.ex_class_id);
    url.addParam("event", options.event);
    url.addParam("_element_id", this.container.identify());
    url.requestUpdate(this.container, options);
  },
  
  refresh: function(){
    ExObject.register(ExObject.container);
  }
};
