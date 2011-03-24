ExObject = {
  container: null,
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
  
    $V(target, data.parser.evaluate(values));
  }
};

ExObjectFormula.parser.ops1 = Object.extend({
  H:   function (ms) { return Math.ceil(ms / Date.hour) },
  J:   function (ms) { return Math.ceil(ms / Date.day) },
  Sem: function (ms) { return Math.ceil(ms / Date.week) },
  M:   function (ms) { return Math.ceil(ms / Date.month) },
  A:   function (ms) { return Math.ceil(ms / Date.year) }
}, ExObjectFormula.parser.ops1);
