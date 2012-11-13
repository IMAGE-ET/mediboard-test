exClassTabs = null;

ExClass = {
  id: null,
  layourEditorReady: false,
  pickMode: true,
  setPickMode: function(active){
    this.pickMode = active;
    getForm('form-grid-layout').setClassName("pickmode", active);
  },
  edit: function(id) {
    this.id = id || this.id;
    
    MbObject.edit("CExClass-"+id, {
      onComplete:function(){
        if (ExField.latest._id && ExField.latest._ex_class_id == id) {
          ExField.edit(ExField.latest._id);
        }
      }
    });
  }
};

ExField = {
  latest: {},
  edit: function(id, ex_class_id, target, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    
    var url = new Url("forms", "ajax_edit_ex_field");
    
    url.addParam("ex_field_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate(target || "exFieldEditor");
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_class_id, ex_group_id) {
    this.edit("0", ex_class_id, null, ex_group_id);
  }
};



ExList = {
  createInModal: function(){
    var url = new Url("forms", "view_ex_list");
    url.addParam("object_guid", "CExList-0");
    url.addParam("hide_tree", 1);
    url.modal({width: 800, height: 600});
  }
};

ExMessage = {
  edit: function(id, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    var url = new Url("forms", "ajax_edit_ex_message");
    url.addParam("ex_message_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor", function(){
      $$("tr[data-ex_class_message_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExSubgroup = {
  edit: function(id, ex_group_id) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    var url = new Url("forms", "ajax_edit_ex_subgroup");
    url.addParam("ex_subgroup_id", id);
    url.addParam("ex_group_id", ex_group_id);
    url.requestUpdate("exFieldEditor", function(){
      $$("tr[data-ex_subgroup_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_group_id) {
    this.edit("0", ex_group_id);
  }
};

ExConcept = Object.clone(ExField);

ExConcept.refreshList =  function(){
  var url = new Url("forms", "ajax_list_ex_concept");
  url.requestUpdate("exConceptList");
};

ExConcept.editCallback = function(id, obj) {
  ExConcept.edit(id, null, "exClassEditor");
  ExConcept.refreshList();
};

ExConcept.createInModal = function(id){
  var url = new Url("forms", "view_ex_concept");
  url.addParam("object_guid", "CExConcept-"+(id || 0));
  url.addParam("hide_tree", 1);
  url.modal({width: 800, height: 600});
  
  /*
  var callback = function(){
    var cont = url.modalObject.container;
    var iframe = cont.down('iframe');
    iframe.contentWindow.MbObject.editCallback = function(id){
      iframe.onload = callback;
      iframe.src += "&object_guid=CExConcept-"+id;
    }
  }
  
  url.modal({width: 800, height: 600});
  url.modalObject.observe("onRemoteContentLoaded", callback);*/
};

ExFieldSpec = {
  options: {},
  edit: function(form){
    var form_name = form.getAttribute("name");
    
    // stupid IE hack
    if (Prototype.Browser.IE) {
      form_name = form.cloneNode(false).getAttribute("name");
    }
    
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addFormData(form);
    url.addParam("m", "forms"); // needed
    url.addParam("form_name", form_name);
    url.addParam("context_guid", form.get("object_guid"));
    url.requestUpdate("fieldSpecEditor");
  }
};

ExConstraint = {
  edit: function(id, ex_class_event_id) {
    var url = new Url("forms", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_event_id", ex_class_event_id);
    url.requestModal(600, 400);
  },
  create: function(ex_class_event_id) {
    this.edit("0", ex_class_event_id);
  },
  editCallback: function(ex_class_event_id) {
    ExClassEvent.edit(ex_class_event_id);
    Control.Modal.close();
  }
};

ExClassEvent = {
  edit: function(id, ex_class_id) {
    var url = new Url("forms", "ajax_edit_ex_class_event");
    url.addParam("ex_class_event_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exClassEventEditor", function(){
      $$("[data-event_id="+id+"]")[0].addUniqueClassName("selected");
    });
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event_name, parts[1]);
    
    var label = form.down("label[for="+form.name+"_unicity_host] strong");
    if (label) {
      label.update($T(parts[0]));
    }
  }
};

ExConceptSpec = {
  options: {},
  edit: function(form){
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addFormData(form);
    url.addParam("m", "forms"); // needed
    url.addParam("form_name", form.getAttribute("name"));
    url.addParam("context_guid", form.get("object_guid"));
    url.requestUpdate("ExConcept-spec-editor");
  }
};

ExFormula = {
  form: null,
  options: {},
  tokens: [],
  edit: function(ex_field_id){
    var url = new Url("forms", "ajax_edit_ex_formula");
    url.addParam("ex_field_id", ex_field_id);
    url.requestUpdate("fieldFormulaEditor", {onComplete: function(){
      ExFormula.form = getForm("editFieldFormula-"+ex_field_id);
      ExFormula.initTextarea();
    }});
  },
  toggleInsertButtons: function(value, type, field_id){
    value = value && ExFormula.form;
    
    if (!value) {
      $$(".insert-formula").invoke("hide");
    }
    else {
      $$("tr.ex-class-field:not([data-ex_class_field_id='"+field_id+"']) .insert-formula."+type).invoke("show");
    }
  },
  insertText: function(text){
    var field = ExFormula.form._formula;

    field.replaceInputSelection(text);
    
    var value = $V(field);
    var pos = value.indexOf('¤');
    if (pos != -1) {
      $V(field, value.replace(/¤/g, ""));
      field.setInputSelection(pos, pos);
    }
  },
  checkTokens: function() {
    var text = $V(ExFormula.form._formula);
    var re = /\[([^\]]+)\]/g;
    var match, bad = [];
    
    while (match = re.exec(text)) {
      if (ExFormula.tokens.indexOf(match[1].strip()) == -1) {
        bad.push(match[1]);
      }
    }
    
    return bad;
  },
  sumAllFields: function(){
    var field = ExFormula.form._formula;
    if ($V(field) && !confirm("Voulez-vous remplacer la formule actuelle ?")) {
      return false;
    }
    
    var tokens = [];
    ExFormula.tokens.each(function(t){
      tokens.push('['+t+']');
    });
    
    $V(field, tokens.join(' + '));
  },
  
  initTextarea: function(){
    var field = ExFormula.form._formula;
    
    field.observe("keyup", function(){
      var bad = ExFormula.checkTokens();
      var message = $("formula-unknown-fields").hide();
      
      if (bad.length == 0) return;
      
      message.show().down("strong").update('"'+bad.join('", "')+'"');
    });

    // Auto-select entire tokens
    field.observe("click", function(){
      var c = field.getInputSelection();
      var text = $V(field).split("");
      var newC = {start: null, end: null};
      
      // find the beginning
      for (var i = c.start; i >= 0; i--) {
        if (text[i] == '[') {
          newC.start = i;
          break;
        }
        
        if (text[i] == ']') return;
      }
      
      // find the end
      for (var i = c.start; i < text.length; i++) {
        if (text[i] == ']') {
          newC.end = i+1;
          break;
        }
        
        if (text[i] == '[') return;
      }
      
      if (newC.start !== null && newC.end !== null)
        field.setInputSelection(newC.start, newC.end);
    });
  }
};

ExFieldPredicate = {
  edit: function(id, ex_field_id, exclude_ex_field_id, form) {
    var url = new Url("forms", "ajax_edit_ex_field_predicate");
    url.addParam("ex_field_predicate_id", id);
    
    if (ex_field_id) {
      url.addParam("ex_field_id", ex_field_id);
    }
    
    if (exclude_ex_field_id) {
      url.addParam("exclude_ex_field_id", exclude_ex_field_id);
    }
    
    if (form && id == 0) {
      url.addParam("opener_field_value", form.predicate_id.identify());
      url.addParam("opener_field_view",  form.predicate_id_autocomplete_view.identify());
    }
    
    url.requestModal(600, 300);
    
    return false;
  },
  create: function(ex_field_id, exclude_ex_field_id, form) {
    this.edit("0", ex_field_id, exclude_ex_field_id, form);
  }
};

ExFieldProperty = {
  edit: function(id, object_class, object_id, form) {
    var url = new Url("forms", "ajax_edit_ex_field_property");
    url.addParam("ex_field_property_id", id);
    
    if (object_id && object_class) {
      url.addParam("object_class", object_class);
      url.addParam("object_id",    object_id);
    }
    
    if (form && id == 0) {
      url.addParam("opener_field_value", form.predicate_id.identify());
      url.addParam("opener_field_view",  form.predicate_id_autocomplete_view.identify());
    }
    
    url.requestModal(600, 300);
  },
  create: function(object_class, object_id, form) {
    this.edit("0", object_class, object_id, form);
  }
};
