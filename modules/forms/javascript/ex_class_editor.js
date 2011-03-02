exClassTabs = null;

ExClass = {
  id: null,
  edit: function(id) {
    this.id = id || this.id;
    var url = new Url("forms", "ajax_edit_ex_class");
    url.addParam("ex_class_id", this.id);
    url.requestUpdate("exClassEditor");
  },
  editCallback: function(id) {
    ExClass.edit(id);
    ExClass.refreshList();
  },
  refreshList: function(){
    var url = new Url("forms", "ajax_list_ex_class");
    url.requestUpdate("exClassList");
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event, parts[1]);
  },
  submitLayout: function(drag, drop) {
    var coord_x = drop.get("x"),
        coord_y = drop.get("y"),
        type    = drag.get("type"),
        form = getForm("form-layout-field");
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_field_id, drag.get("field_id"));
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    form.onsubmit();
    drop.insert(drag);
  },
  submitLayoutHostField: function(drag, drop) {
    var coord_x = drop.get("x"),
        coord_y = drop.get("y"),
        type    = drag.get("type"),
        form = getForm("form-layout-hostfield");
        
    if (!drop.hasClassName("droppable")) return;
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_host_field_id, drag.get("field_id") || "");
    $V(form.elements.field, drag.get("field") || "");
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    $V(form.elements.callback, "");
    $V(form.del, 0);
			
    // dest = LIST
    if (drop.hasClassName("out-of-grid")) {
      var del = drag.get("field_id");
			
      $V(form.elements.callback, "");
			
      if (del) {
        drag.remove();
        $V(form.del, 1);
      }
      else {
        return;
      }
    }
    
    // dest = GRID
    else {
      if (!drag.up(".grid")) {
        drag = drag.clone(true);
        ExClass.initDraggableHostField(drag);
        drag.setStyle({
          position: "static",
          opacity: 1
        });
      }
      drop.update(drag);
			
			var id = drag.identify();
			$V(form.elements.callback, "ExClass.setHostFieldId.curry("+id+")");
    }
    
    onSubmitFormAjax(form);
  },
	setHostFieldId: function(element_id, object_id, obj) {
		var drag = $(element_id);
		if (drag) {
			drag.setAttribute("data-field_id", object_id);
		}
	},
  initDraggableHostField: function(d){
    new Draggable(d, {
      revert: 'failure', 
      scroll: window, 
      ghosting: true,
      onStart: function(drag){
        drag.element.addClassName("dragging");
        $$(".out-of-grid")[0].addClassName("dropactive");
      },
      onEnd: function(drag){
        drag.element.removeClassName("dragging");
        $$(".out-of-grid")[0].removeClassName("dropactive");
      }
    });
  },
  initLayoutEditor: function(){
    $$(".draggable:not(.hostfield)").each(function(d){
      new Draggable(d, {
        revert: 'failure', 
        scroll: window, 
        ghosting: true,
        onStart: function(){$$(".out-of-grid")[0].addClassName("dropactive")},
        onEnd: function(){$$(".out-of-grid")[0].removeClassName("dropactive")}
      });
    });
    
    $$(".draggable.hostfield").each(ExClass.initDraggableHostField);
    
    /*
    $$(".draggable.hr").each(function(d){
      new Draggable(d, {
        //revert: true, 
        scroll: window, 
        ghosting: true
      });
    });*/
    
    $$(".droppable").each(function(d){
      Droppables.add(d, {
        hoverclass: 'dropover',
        onDrop: function(drag, drop) {
          drag.style.position = ''; // a null value doesn't work on IE
          
          // prevent multiple fields in the same cell
          if (drop.hasClassName('grid') && drop.select('.draggable').length) return;
            
          // grid to trash for ExFields
          if (drop.hasClassName("out-of-grid") && !drag.hasClassName('hostfield')) {
            if (drag.hasClassName('field')) {
              drop = drop.down(".field-list");
            }
            
            if (drag.hasClassName('label')) {
              drop = drop.down(".label-list");
            }
          }
          
          if (drag.hasClassName('hostfield')) {
            ExClass.submitLayoutHostField(drag, drop);
          }
          else {
            ExClass.submitLayout(drag, drop);
          }
        }
      });
    });
    
    if (Prototype.Browser.IE) {
      (function(){
        getForm("form-grid-layout").select("input, select").invoke("disable");
      }).defer();
    }
  }
};

ExField = {
  edit: function(id, ex_class_id, target) {
    if (window.exClassTabs) {
      exClassTabs.setActiveTab("fields-specs");
    }
    
    var url = new Url("forms", "ajax_edit_ex_field");
    
    url.addParam("ex_field_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate(target || "exFieldEditor");
  },
  editCallback: function(id, obj) {
    // void
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  },
  slug: function(str) {
    str = (str+"")
      .strip()
      .removeDiacritics() // Suppression des accents
      .toLowerCase() // En minuscule
      .replace(/@/g, '_at_') // Petit bonus
      .replace(/['"\(\)\{\}]/g, '') // Suppression des caractères courants
      .replace(/[^a-z0-9_]/g, '_') // Dernier nettoyage
      .replace(/^[0-9_]+/g, '') // Suppression des chiffres et underscore au début
      .replace(/_+/g, '_'); // Suppression des underscore répétés
      
    return str;
  }
};

ExConcept = ExField;

ExConcept.refreshList =  function(){
  var url = new Url("forms", "ajax_list_ex_concept");
  url.requestUpdate("exConceptList");
};

ExConcept.editCallback = function(id, obj) {
  ExConcept.refreshList();
  ExConcept.edit(id, null, "exClassEditor");
};

ExFieldSpec = {
  options: {},
  edit: function(formField, prop, className, field, otherFields, ex_field_id){
    var specType = $V(formField);
    var match = specType.match(/[a-z]+-(\d+)/);
    
    if (match) {
      $V(formField.form.concept_id, match[1]);
    }
    else {
      $V(formField.form.concept_id, "");
    }
    
    var url = new Url("forms", "ajax_edit_ex_field_spec");
    url.addParam("spec_type", specType);
    url.addParam("prop", prop);
    url.addParam("class", className);
    url.addParam("field", field);
    url.addParam("other_fields", otherFields, true);
    url.addParam("ex_field_id", ex_field_id);
    url.requestUpdate("fieldSpecEditor");
  }
};

ExConstraint = {
  edit: function(id, ex_class_id) {
    var url = new Url("forms", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exConstraintEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  }
};

ExConceptSpec = {
  options: {},
  edit: function(form){
    var prop = $V(form.prop);
    var spec_type = $V(form._spec_type);
    var ex_list_id = $V(form.ex_list_id);
		
    var url = new Url("forms", "ajax_edit_ex_field_spec2");
    url.addParam("spec_type", spec_type);
    url.addParam("prop", prop);
    url.addParam("ex_list_id", ex_list_id);
    url.addParam("form_name", form.getAttribute("name"));
    url.addParam("owner_guid", form.get("object_guid"));
    url.requestUpdate("ExConcept-spec-editor");
  }
};
