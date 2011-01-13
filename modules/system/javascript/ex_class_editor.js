
var ExClass = {
  id: null,
  edit: function(id) {
    this.id = id || this.id;
    var url = new Url("system", "ajax_edit_ex_class");
    url.addParam("ex_class_id", this.id);
    url.requestUpdate("exClassEditor");
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event, parts[1]);
  },
  submitLayout: function(drag, drop) {
    var form = getForm("form-layout-field"),
        coord_x = drop.getAttribute("data-x"),
        coord_y = drop.getAttribute("data-y"),
        type = drag.hasClassName("field") ? "field" : "label";
    
    $(form).select('input.coord').each(function(coord){
      $V(coord.disable(), '');
    });
    
    $V(form.ex_class_field_id, drag.getAttribute("data-field_id"));
    $V(form["coord_"+type+"_x"].enable(), coord_x);
    $V(form["coord_"+type+"_y"].enable(), coord_y);
    
    form.onsubmit();
  },
	initLayoutEditor: function(){
    $$(".draggable:not(.hr)").each(function(d){
      new Draggable(d, {
        //revert: true, 
        scroll: window, 
        ghosting: true,
        onStart: function(){$$(".out-of-grid")[0].addClassName("dropactive")},
        onEnd: function(){$$(".out-of-grid")[0].removeClassName("dropactive")}
      });
    });
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
          
          if (drop.hasClassName("out-of-grid")) {
            if (drag.hasClassName('field')) {
              drop = drop.down(".field-list");
            }
            
            if (drag.hasClassName('label')) {
              drop = drop.down(".label-list");
            }
          }
          
          else {
            // prevent multiple fields in the same cell
            if (drop.hasClassName('grid') && drop.select('.draggable').length) return;
          }
          
          drop.insert(drag);
          ExClass.submitLayout(drag, drop);
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

var ExField = {
  edit: function(id, ex_class_id) {
    var url = new Url("system", "ajax_edit_ex_field");
    url.addParam("ex_field_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exFieldEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  }
};

var ExFieldSpec = {
  options: {},
  edit: function(specType, prop, className, field, otherFields, ex_field_id){
    var url = new Url("system", "ajax_edit_ex_field_spec");
    url.addParam("spec_type", specType);
    url.addParam("prop", prop);
    url.addParam("class", className);
    url.addParam("field", field);
    url.addParam("other_fields", otherFields, true);
    url.addParam("ex_field_id", ex_field_id);
    url.requestUpdate("fieldSpecEditor");
  }
};

var ExConstraint = {
  edit: function(id, ex_class_id) {
    var url = new Url("system", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exConstraintEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  }
};