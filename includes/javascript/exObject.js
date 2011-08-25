ExObject = {
  container: null,
  classes: {},
  register: function(container, options) {
    this.container = $(container);
    
    if (!this.container) {
      return;
    }
    
    options = Object.extend({
      ex_class_id: null,
      object_guid: null,
      event: null
    }, options);
    
    var url = new Url("forms", "ajax_widget_ex_classes_new");
    url.addParam("object_guid", options.object_guid);
    url.addParam("ex_class_id", options.ex_class_id);
    url.addParam("event", options.event);
    url.addParam("_element_id", this.container.identify());
    url.requestUpdate(container, options);
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
        showExClassForm(id, object_guid, /*object_guid+"_"+*/event+"_"+id, "", event);
      });
      options.onTriggered(object_guid, event);
    });
  },
  
  triggerMulti: function(forms) {
    $A(forms).each(function(data){
      showExClassForm(data.ex_class_id, data.object_guid, /*object_guid+"_"+*/data.event+"_"+data.ex_class_id, "", data.event);
    });
  },
  
  initTriggers: function(triggers, form, elementName, parent_view){
    var inputs = Form.getInputsArray(form[elementName]);
    
    var triggerFunc = function(input, triggers) {
      var isSetCheckbox = input.hasClassName("set-checkbox");
      
      if (isSetCheckbox && !input.checked) {
        return;
      }
      
      var value = (isSetCheckbox ? input.value : $V(input));
      var ex_class_id = triggers[value];
      triggers[value] = null;
      
      if (ex_class_id) {
        var form = input.form;
        var object_guid = ExObject.current.object_guid;
        var event = ExObject.current.event;
        showExClassForm(ex_class_id, object_guid, /*object_guid+"_"+*/event+"_"+ex_class_id, "", event, null, parent_view);
      }
    }
    
    inputs.each(function(input){
      var callback = triggerFunc.curry(input, triggers);
      input.observe("change", callback)
           .observe("ui:change", callback)
           .observe("click", callback);
    });
  },
  
  show: function(mode, ex_object_id, ex_class_id, object_guid){
    var url = new Url("forms", "view_ex_object_form");
    url.addParam("ex_object_id", ex_object_id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("object_guid", object_guid);
    
    if (mode == "display" || mode == "print") {
      url.addParam("readonly", 1);
    }
    
    if (mode == "print") {
      url.addParam("print", 1);
      url.addParam("only_filled", 1);
    }
    
    url.pop("100%", "100%", mode+"-"+ex_object_id);
  },
  
  print: function(ex_object_id, ex_class_id, object_guid){
    ExObject.show("print", ex_object_id, ex_class_id, object_guid);
  },
  
  display: function(ex_object_id, ex_class_id, object_guid){
    ExObject.show("display", ex_object_id, ex_class_id, object_guid);
  },
  
  edit: function(ex_object_id, ex_class_id, object_guid){
    ExObject.show("edit", ex_object_id, ex_class_id, object_guid);
  },
  
  history: function(ex_object_id, ex_class_id){
    var url = new Url("system", "view_history");
    url.addParam("object_class", "CExObject");
    url.addParam("object_id", ex_object_id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("user_id", "");
    url.addParam("type", "");
    url.popup(900, 600, "history");
  },
  
  loadExObjects: function(object_class, object_id, target, detail, ex_class_id, options) {
    detail = detail || 0;
    ex_class_id = ex_class_id || "";
    
    options = Object.extend({
      print: 0
    }, options);
    
    var url = new Url("forms", "ajax_list_ex_object");
    url.addParam("detail", detail);
    url.addParam("reference_id", object_id);
    url.addParam("reference_class", object_class);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("target_element", target);
    url.mergeParams(options);
    url.requestUpdate(target);
  }
};

ExObjectFormula = {
  tokenData: null,
  parser: new Parser,
  
  init: function(tokenData) {
    ExObjectFormula.tokenData = tokenData;
    
    $H(ExObjectFormula.tokenData).each(function(token){
      var field = token.key;
      var data = token.value;
      var formula = data.formula;
      
      if (!formula) return;
  
      formula = formula.replace(/[\[\]]/g, "");
      
      var form = getForm("editExObject");
      var fieldElement = form[field];
      
      try {
        var expr = ExObjectFormula.parser.parse(formula);
        var variables = expr.variables();
      }
      catch(e) {
        fieldElement.insert({after: DOM.div({className: 'small-error'}, "Formule invalide: <br /><strong>"+data.formulaView+"</strong>")});
        return;
      }
  
      ExObjectFormula.tokenData[field].parser = expr;
      ExObjectFormula.tokenData[field].variables = variables;
  
      function compute(target) {
        ExObjectFormula.computeResult(target);
      }
      
      variables.each(function(v){
        if (!form[v]) return;
        
        var inputs = Form.getInputsArray(form[v]);
        
        inputs.each(function(input){
          if (input.hasClassName("date") || input.hasClassName("dateTime") || input.hasClassName("time")) {
            input.onchange = compute.curry(fieldElement);
          }
          else {
            var callback = compute.curry(fieldElement);
            input.observe("change", callback)
                 .observe("ui:change", callback)
                 .observe("click", callback);
          }
        });
      });
      
      ExObjectFormula.computeResult(fieldElement);
    });
  },
  
  //get the input value : coded or non-coded
  getInputValue: function(element){
    var value = $V(element);
    
    element = Form.getInputsArray(element)[0];
    
    var name = element.name;
    var result = ExObjectFormula.tokenData[name].values;

    if (element.hasClassName("date")) {
      if (!value) return NaN;
      var date = Date.fromDATE(value);
      date.resetTime();
      return date.getTime();
    }

    if (element.hasClassName("dateTime")) {
      if (!value) return NaN;
      var date = Date.fromDATETIME(value);
      return date.getTime();
    }
    
    if (element.hasClassName("time")) {
      if (!value) return NaN;
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

  //computes the result of a form + exGroup(formula, resultField)
  computeResult: function(target){
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
      if (values[v] === "") values[v] = NaN;
    });
  
    var result = data.parser.evaluate(values);
    if (!isFinite(result)) {
      result = "";
    }
    else {
      var props = target.getProperties();
      if (props.decimals) {
        result = (result*1).toFixed(props.decimals);
      }
    }
    
    $V(target, (result+""));
  }
};

ExObjectFormula.parser.ops1 = Object.extend({
  Min: function (ms) { return Math.ceil(ms / Date.minute) },
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
showExClassForm = function(ex_class_id, object_guid, title, ex_object_id, event, _element_id, parent_view) {
  var url = new Url("forms", "view_ex_object_form");
  url.addParam("ex_class_id", ex_class_id);
  url.addParam("object_guid", object_guid);
  url.addParam("ex_object_id", ex_object_id);
  url.addParam("event", event);
  url.addParam("_element_id", _element_id);
  url.addParam("parent_view", parent_view);

  var _popup = true;//Control.Overlay.container && Control.Overlay.container.visible();

  if (_popup) {
    url.popup("100%", "100%", title);
  }
  else {
    url.modal({title: title});
    url.modalObject.observe("afterClose", function(){
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
