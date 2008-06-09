// $Id: $

var PrescriptionEditor = {
  sForm     : null,
  options : {
    width : 810,
    height: 600
  },
  popup : function(prescription_id, object_id, object_class, type) {
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_vw_prescription");
      url.addParam("prescription_id", prescription_id);
      url.addParam("popup", "1");
      url.addParam("full_mode", "1");
      url.addParam("object_id", object_id);
      url.addParam("object_class", object_class);
      url.addParam("type", type);
      url.popup(this.options.width, this.options.height, "Prescription");
  },
  refresh: function(object_id, object_class, praticien_id){
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_widget_prescription");
    
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("praticien_id", praticien_id);
    
    Prescription.suffixes.each( function(suffixe) {
	    url.addParam("suffixe", suffixe);
	    url.make();
	    if($('prescription-'+object_class+'-'+suffixe)){
	      url.requestUpdate("prescription-"+object_class+"-"+suffixe, { waitingText : null } );
      }
    } );
  },
  register: function(object_id, object_class, suffixe, praticien_id){
    // document.write cause des erreurs dans le cas de la DHE
    // document.write('<div id=prescription-'+object_class+'-'+suffixe+'></div>');
    $('prescription_register').insert('<div id=prescription-'+object_class+'-'+suffixe+'></div>');
    Main.add( function() {
      Prescription.suffixes.push(suffixe);
	    PrescriptionEditor.refresh(object_id, object_class, praticien_id);
    } );
    
  }
}
