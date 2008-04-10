// $Id: $

var PrescriptionEditor = {
  sForm     : null,
  options : {
    width : 810,
    height: 600
  },
  popup : function(prescription_id, object_id, object_class, praticien_id, type) {
      var url = new Url;
      url.setModuleAction("dPprescription", "vw_edit_prescription");
      url.addParam("prescription_id", prescription_id);
      url.addParam("object_id", object_id);
      url.addParam("object_class", object_class);
      url.addParam("praticien_id", praticien_id);
      if(type){
        url.addParam("type", type);
      }
      url.addParam("popup", "1");
      url.popup(this.options.width, this.options.height, "Prescription");
  },
  refresh: function(prescription_id, object_class){

    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_widget_prescription");
    url.addParam("prescription_id", prescription_id);
    
    Prescription.suffixes.each( function(suffixe) {
	    url.addParam("suffixe", suffixe);
	    url.make();
	    if($('prescription-'+object_class+'-'+suffixe)){
	      url.requestUpdate("prescription-"+object_class+"-"+suffixe, { waitingText : null } );
      }
    } );
  
  } 
}
