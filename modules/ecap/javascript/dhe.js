var DHE = {
  refresh: function(patient_id, praticien_id) {
    var url = new Url;
    url.setModuleAction("ecap", "httpreq_new_dhe");
    url.addParam("patient_id", patient_id);
    url.addParam("praticien_id", praticien_id);
    url.requestUpdate("dhe-form", { waitingText: null } );
  },
  
  register: function(patient_id, praticien_id, container) {
    var div = document.createElement("div");
    div.style.minWidth = "200px";
    div.style.minHeight = "30px";
    div.id = "dhe-form";
    $(container).insert(div);
    
    Main.add( function() {
      DHE.refresh(patient_id, praticien_id);
    } );
  }
};