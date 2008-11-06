function initMedecinAutocomplete(formName, fieldName, doFocus) {
  var form = getForm(formName);
  var field = $(form[fieldName]);
  var choices = formName+'_'+fieldName+'_autocomplete';
  Assert.that(field, "Medecin field '%s'is missing", fieldName);
  Assert.that($(choices), "Medecin complete div '%s'is missing", choices);

  new Ajax.Autocompleter(
    field,
    choices,
    '?m=dPpatients&ajax=1&suppressHeaders=1&a=httpreq_do_medecins_autocomplete&keywords_field='+fieldName, {
      method: 'get',
      minChars: 2,
      frequency: 0.15,
      updateElement : function(element) {
        $V((form.medecin_id ? form.medecin_id : form.medecin_traitant), element.id.split('-')[1]);
      }
    }
  );
}

var Correspondants = Correspondants || Class.create({
  initialize: function (patient_id, options) {
    this.module = "dPpatients";
    this.action = "httpreq_widget_correspondants";
    
    this.patient_id = patient_id;
    this.options = Object.extend({
      container: null,
      popup: false
    }, options || {});
    
    this.widget_id = "correspondants-patient_id-"+this.patient_id;
    
    if ($(this.widget_id)) {
      Console.error("A widget with this id ("+this.widget_id+") already exists");
    }
    
    var widget = '<div id="'+this.widget_id+'"></div>';

    if (this.options.container) {
      $(this.options.container).insert(widget);
    }
    else {
      document.write(widget);
    }
    
    $(this.widget_id).widget = this;
    //if (!this.options.popup) {
      this.refresh();
    /*}
    else {
      var button = new Element('button', {className: 'new', type: 'button'}).update("Médecins correspondants");
      var that = this;
      button.observe('click', function() {that.popup()});
      $(this.widget_id).update(button);
    }*/
  },
  
  /*popup: function() {
    var url = new Url;
    url.setModuleAction(this.module, this.action);
    url.addParam("patient_id", this.patient_id);
    url.addParam("widget_id", this.widget_id);
    url.popup(600, 400, "Correspondants");  
  },*/
  
  refresh: function() {
    var url = new Url;
    url.setModuleAction(this.module, this.action);
    url.addParam("patient_id", this.patient_id);
    url.addParam("widget_id", this.widget_id);
    url.requestUpdate(this.widget_id, { waitingText : null });
  }
});
