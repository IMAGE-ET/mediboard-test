ExObject = {
  container: null,
  classes: {},
  register: function(container, options) {
    this.container = $(container);
    
    options = Object.extend({
      ex_class_id: null,
      object_guid: null,
      event: null
    }, options);
    
    var url = new Url("forms", "ajax_widget_ex_classes");
    url.addParam("object_guid", options.object_guid);
    url.addParam("ex_class_id", options.ex_class_id);
    url.addParam("event", options.event);
    url.addParam("_element_id", this.container.identify());
    url.requestUpdate(this.container, options);
  },
  
  refresh: function(){
    ExObject.register(ExObject.container);
  },
  
  trigger: function(object_guid, event, options) {
    options = Object.extend({
      onTriggered: function(){}
    }, options);
    
    var url = new Url("forms", "ajax_trigger_ex_classes");
    url.addParam("object_guid", object_guid);
    url.addParam("event", event);
    url.requestJSON(function(ex_classes_id){
      ex_classes_id.each(function(id){
        showExClassForm(id, object_guid, object_guid+"-"+event+"-"+id, "", event);
      });
      options.onTriggered(object_guid, event);
    });
  }
};

ExObjectFormula = {
  tokenData: null,
  parser: new Parser,
  
  //get the input value : coded or non-coded
  getInputValue: function(element){
    var value = $V(element);
    
    element = ExObjectFormula.getInputElementsArray(element)[0];
    
    var name = element.name;
    var result = ExObjectFormula.tokenData[name].values;

    if (element.hasClassName("date")) {
      if (!value) return null;
      var date = Date.fromDATE(value);
      date.resetTime();
      return date.getTime();
    }

    if (element.hasClassName("dateTime")) {
      if (!value) return null;
      var date = Date.fromDATETIME(value);
      return date.getTime();
    }
    
    if (element.hasClassName("time")) {
      if (!value) return null;
      var date = Date.fromDATETIME("1970-01-01 "+value);
      date.resetDate();
      return date.getTime();
    }
    
    // non-coded
    if (result === true)
      return value;

    // coded
    return ExObjectFormula.tokenData[name].values[value];
  },
  
  getInputElementsArray: function(element){
    if (element instanceof NodeList || element instanceof HTMLCollection) {
     return $A(element);
    }

    return [element];
  },

  //computes the result of a form + exGroup(formula, resultField)
  computeResult: function(input, target){
    var data = ExObjectFormula.tokenData[target.name];
    var form = target.form;
    
    var date = (new Date());
    date.resetTime();
    date = date.getTime();
    
    var time = (new Date());
    time.resetDate();
    time = time.getTime();
    
    var now = (new Date()).getTime();
    
    var constants = {
      DateCourante: date,
      HeureCourante: time,
      DateHeureCourante: now
    };
    var values = {};

    data.variables.each(function(v){
      values[v] = constants[v] || ExObjectFormula.getInputValue(form[v]);
    });
  
    var result = data.parser.evaluate(values);
    if (isNaN(result)) {
      result = "";
    }
    
    $V(target, (result+""));
  }
};

ExObjectFormula.parser.ops1 = Object.extend({
  H:   function (ms) { return Math.ceil(ms / Date.hour) },
  J:   function (ms) { return Math.ceil(ms / Date.day) },
  Sem: function (ms) { return Math.ceil(ms / Date.week) },
  M:   function (ms) { return Math.ceil(ms / Date.month) },
  A:   function (ms) { return Math.ceil(ms / Date.year) }
}, ExObjectFormula.parser.ops1);


// TODO put this in the object
selectExClass = function(element, object_guid, event, _element_id) {
  var view = element.options ? element.options[element.options.selectedIndex].innerHTML : element.innerHTML;
  showExClassForm($V(element) || element.value, object_guid, view, null, event, _element_id);
  element.selectedIndex = 0;
}
showExClassForm = function(ex_class_id, object_guid, title, ex_object_id, event, _element_id) {
  var url = new Url("forms", "view_ex_object_form");
  url.addParam("ex_class_id", ex_class_id);
  url.addParam("object_guid", object_guid);
  url.addParam("ex_object_id", ex_object_id);
  url.addParam("event", event);
  url.addParam("_element_id", _element_id);

  var _popup = true;//Control.Overlay.container && Control.Overlay.container.visible();

  if (_popup) {
    url.popup("100%", "100%", title);
  }
  else {
    url.modale({title: title});
    url.modaleObject.observe("afterClose", function(){
      ExObject.register(_element_id, {
        ex_class_id: ex_class_id, 
        object_guid: object_guid, 
        event: event, 
        _element_id: _element_id
      });
    });
  }
}
////////////////////
