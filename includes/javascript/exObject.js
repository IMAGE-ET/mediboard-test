var ExObject = {
  container: null,
  classes: {},
  refreshSelf: {},
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
    
    // Multiple objects
    if (Object.isArray(object_guid)) {
      var url = new Url("forms", "ajax_trigger_ex_classes_multiple");
      url.addParam("object_guids[]", object_guid, true);
      url.addParam("event", event);
      url.requestJSON(function(datas){
        datas.reverse(false).each(function(data){
          showExClassForm(data.ex_class_id, data.object_guid, data.object_guid+"_"+data.event+"_"+data.ex_class_id, "", data.event);
        });
        
        options.onTriggered(datas, event);
      });
    }
    
    // Single objects
    else {
      var url = new Url("forms", "ajax_trigger_ex_classes");
      url.addParam("object_guid", object_guid);
      url.addParam("event", event);
      url.requestJSON(function(ex_classes_id){
        ex_classes_id.each(function(id){
          showExClassForm(id, object_guid, /*object_guid+"_"+*/event+"_"+id, "", event);
        });
        
        options.onTriggered(object_guid, event);
      });
    }
  },
  
  triggerMulti: function(forms) {
    $A(forms).each(function(data){
      showExClassForm(data.ex_class_id, data.object_guid, data.object_guid+"_"+data.event+"_"+data.ex_class_id, "", data.event);
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
  
  show: function(mode, ex_object_id, ex_class_id, object_guid, element_id){
    var url = new Url("forms", "view_ex_object_form");
    url.addParam("ex_object_id", ex_object_id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("object_guid", object_guid);
    
    if (element_id) {
      url.addParam("_element_id", element_id);
    }
    
    if (mode == "display" || mode == "print") {
      url.addParam("readonly", 1);
    }
    /*else {
      window["callback_"+ex_class_id] = function(ex_class_id, object_guid){
        ExObject.register(this.container, {
          ex_class_id: ex_class_id, 
          object_guid: object_guid, 
          event: event
        });
      }.bind(this).curry(ex_class_id, object_guid);
    }*/
    
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
  
  edit: function(ex_object_id, ex_class_id, object_guid, element_id){
    ExObject.show("edit", ex_object_id, ex_class_id, object_guid, element_id);
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
      print: 0,
      start: 0,
      search_mode: null,
      onComplete: function(){}
    }, options);
    
    target = $(target);
    
    target.writeAttribute("data-reference_class", object_class);
    target.writeAttribute("data-reference_id",    object_id);
    target.writeAttribute("data-ex_class_id",     ex_class_id);
    target.writeAttribute("data-detail",          detail);
    
    var url = new Url("forms", "ajax_list_ex_object");
    url.addParam("detail", detail);
    url.addParam("reference_id", object_id);
    url.addParam("reference_class", object_class);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("target_element", target.identify());
    url.mergeParams(options);
    url.requestUpdate(target, {onComplete: options.onComplete});
  },
  getCastedInputValue: function(value, input){
    if (input.hasClassName("float") || 
        input.hasClassName("currency") || 
        input.hasClassName("pct")) {
      return parseFloat(value);
    }
    
    if (input.hasClassName("num") || 
        input.hasClassName("numchar") || 
        input.hasClassName("pct")) {
      return parseInt(value);
    }
    
    return value;
  },
  handlePredicates: function(predicates, form){
    predicates.each(function(p){
      var triggerField = form.elements[p.trigger];
      var targetField  = form.elements[p.target];
      var triggerValue = $V(triggerField);
      
      if (Object.isArray(triggerValue)) {
        triggerValue = triggerValue.join("|");
      }
      else {
        triggerValue += "";
      }
      
      var refValue = p.value;
      
      if ([">", ">=", "<", "<="].indexOf(p.operator) > -1) {
        refValue     = ExObject.getCastedInputValue(p.value, triggerField);
        triggerValue = ExObject.getCastedInputValue(triggerValue, triggerField);
      }
      
      var display = (function(){
        // An empty value hides the target
        if (triggerValue === "" || isNaN(triggerValue)) {
          return false;
        }
        
        switch (p.operator) {
          case "=": 
            if (triggerValue == p.value) return true;
            break;
            
          case "!=": 
            if (triggerValue != p.value) return true;
            break;
            
          case ">": 
            if (triggerValue > refValue) return true;
            break;
            
          case ">=": 
            if (triggerValue >= refValue) return true;
            break;
            
          case "<": 
            if (triggerValue < refValue) return true;
            break;
            
          case "<=": 
            if (triggerValue <= refValue) return true;
            break;
            
          case "startsWith": 
            if (triggerValue.indexOf(p.value) == 0) return true;
            break;
            
          case "endsWith": 
            if (triggerValue.substr(-p.value.length) == p.value) return true;
            break;
            
          case "contains": 
            if (triggerValue.indexOf(p.value) > -1) return true;
            break;
            
          default: return true;
        }
        
        return false;
      })();
      
      ExObject.toggleField(p.target, display, triggerField, targetField);
    });
  },
  
  initPredicates: function(predicates, form){
    var callback = ExObject.handlePredicates.curry(predicates, form);
    callback();
    
    predicates.each(function(p){
      var inputs = Form.getInputsArray(form.elements[p.trigger]);
      
      inputs.each(function(input){
        input.observe("change", callback)
             .observe("ui:change", callback)
             .observe("click", callback);
      });
    });
  },
  
  toggleField: function(name, v, triggerField, targetField) {
    $$("div.field-"+name).each(function(container){
      container.setClassName("opacity-20", !v);
      
      Form.getInputsArray(targetField).each(function(input){
        input.disabled = !v;
      });
      
      if (!v) {
        $V(targetField, "");
      }
    });
  }
};

var ExObjectFormula = Class.create({
  tokenData: null,
  form: null,
  customOps: {
    Min: function (ms) { return Math.ceil(ms / Date.minute) },
    H:   function (ms) { return Math.ceil(ms / Date.hour) },
    J:   function (ms) { return Math.ceil(ms / Date.day) },
    Sem: function (ms) { return Math.ceil(ms / Date.week) },
    M:   function (ms) { return Math.ceil(ms / Date.month) },
    A:   function (ms) { return Math.ceil(ms / Date.year) }
  },
  
  initialize: function(tokenData, form) {
    this.tokenData = tokenData;
    this.form = form;
    this.parser = new Parser;
    
    // Extend Parser with cutom operators (didn't find a way to do this on the prototype) 
    this.parser.ops1 = Object.extend(this.customOps, this.parser.ops1);
    
    var allFields = Object.keys(this.tokenData);
    
    $H(this.tokenData).each(function(token){
      var field = token.key;
      var data = token.value;
      var formula = data.formula;
      
      if (!formula) return;
      
      var fieldElement = this.form[field];
      var compute, variables = [], expr;
      
      // concatenation
      if (fieldElement.hasClassName("text")) {
        fieldElement.value = formula;
        
        allFields.each(function(v){
          if (formula.indexOf("[" + v + "]") != -1) {
            variables.push(v);
          }
        });
        
        expr = {
          evaluate: (function(formula, values){
            var result = formula;
            
            $H(values).each(function(pair){
              result = result.replace(new RegExp("(\\[" + pair.key + "\\])", "g"), pair.value);
            });
            
            return result;
          }).curry(formula)
        };
      }
      
      // arithmetic
      else {
        formula = formula.replace(/[\[\]]/g, "");
        
        try {
          expr = this.parser.parse(formula);
          variables = expr.variables();
        } 
        catch (e) {
          fieldElement.insert({
            after: DOM.div({
              className: 'small-error'
            }, "Formule invalide: <br /><strong>" + data.formulaView + "</strong>")
          });
          return;
        }
      }
      
      this.tokenData[field].parser = expr;
      this.tokenData[field].variables = variables;
      
      compute = this.computeResult.bind(this).curry(fieldElement);
      compute();
      
      variables.each(function(v){
        if (!this.form[v]) 
          return;
        
        var inputs = Form.getInputsArray(this.form[v]);
        
        inputs.each(function(input){
          if (input.hasClassName("date") || 
              input.hasClassName("dateTime") || 
              input.hasClassName("time")) {
            input.onchange = compute;
          }
          else {
            input.observe("change", compute).observe("ui:change", compute).observe("click", compute);
          }
        });
      }, this);
    }, this);
  },
  
  //get the input value : coded or non-coded
  getInputValue: function(element){
    if (!element) return false;
    var value = $V(element);
    
    element = Form.getInputsArray(element)[0];
    
    var name = element.name;
    var result = this.tokenData[name].values;

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
    return this.tokenData[name].values[value];
  },

  //computes the result of a form + exGroup(formula, resultField)
  computeResult: function(target){
    var data = this.tokenData[target.name];
    if (!data) return;
    
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
    var isConcat = target.hasClassName("text");

    data.variables.each(function(v){
      var val = constants[v] || this.getInputValue(form[v]);
      
      // functions are considered like variables
      if (val === false) {
        return;
      }
      
      values[v] = val;
      
      if (!isConcat && values[v] === "") values[v] = NaN;
    }, this);
    
    var result = data.parser.evaluate(values);
    if (!isConcat && !isFinite(result)) {
      result = "";
    }
    else {
      var props = target.getProperties();
      if (props.decimals) {
        result = (result*1).toFixed(props.decimals);
      }
    }
    
    result += "";
    $V(target, result);
    
    if (isConcat) {
      target.rows = result.split("\n").length;
    }
  }
});

// TODO put this in the object
function selectExClass(element, object_guid, event, _element_id) {
  var view = element.options ? element.options[element.options.selectedIndex].innerHTML : element.innerHTML;
  showExClassForm($V(element) || element.value, object_guid, view, null, event, _element_id);
  element.selectedIndex = 0;
}

function showExClassForm(ex_class_id, object_guid, title, ex_object_id, event, _element_id, parent_view, ajax_container) {
  var url = new Url("forms", "view_ex_object_form");
  url.addParam("ex_class_id", ex_class_id);
  url.addParam("object_guid", object_guid);
  url.addParam("ex_object_id", ex_object_id);
  url.addParam("event", event);
  url.addParam("_element_id", _element_id);
  url.addParam("parent_view", parent_view);

  /*window["callback_"+ex_class_id] = function(){
    ExObject.register(_element_id, {
      ex_class_id: ex_class_id, 
      object_guid: object_guid, 
      event: event, 
      _element_id: _element_id
    });
  }*/
    
  var _popup = true;//Control.Overlay.container && Control.Overlay.container.visible();

  ajax_container = null;
  
  if (ajax_container) {
    url.requestUpdate(ajax_container);
    return;
  }
  
  if (_popup) {
    url.popup("100%", "100%", title);
  }
  else {
    url.modal();
  }
}
////////////////////
