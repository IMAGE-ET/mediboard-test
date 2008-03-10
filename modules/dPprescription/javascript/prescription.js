var Prescription = {
	// Multiples occurences de la même widget
  suffixes: [],
  addEquivalent: function(code, line_id){
    Prescription.delLineWithoutRefresh(line_id);
    // Suppression des champs de addLine
    var oForm = document.addLine;
    oForm.prescription_line_id.value = "";
    oForm.del.value = "";
    Prescription.addLine(code);
  },
  close : function(object_id, object_class) {
    var url = new Url;
    url.setModuleTab("dPprescription", "vw_edit_prescription");
    url.addParam("prescription_id", 0);
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.redirect();
  },
  applyProtocole: function(prescription_id, protocole_id){
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_add_protocole_lines");
    url.addParam("prescription_id", prescription_id)
    url.addParam("protocole_id", protocole_id);
    urlPrescription.requestUpdate("produits_elements", { waitingText : null });
  }, 
  addLine: function(code) {
    var oForm = document.addLine;
    oForm.code_cip.value = code;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : 
        function(){ 
          Prescription.reload(oForm.prescription_id.value);
         } 
    });
  },
  addLineElement: function(element_id, category_name){
    var oForm = document.addLineElement;
    oForm.element_prescription_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function(){ Prescription.reload(oForm.prescription_id.value, element_id, category_name) } 
    });
  },
  delLineWithoutRefresh: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg');
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ Prescription.reload(oForm.prescription_id.value) } 
    });
  },
  delLineElement: function(line_id, category_name) {
    var oForm = document.addLineElement;
    oForm.prescription_line_element_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ Prescription.reload(oForm.prescription_id.value, null, category_name) } 
    });
  },
  reload: function(prescription_id, element_id, category_name, mode_protocole) {
      if(window.opener){
      window.opener.PrescriptionEditor.refresh(prescription_id);
      }
      var urlPrescription = new Url;
      urlPrescription.setModuleAction("dPprescription", "httpreq_vw_prescription");
      urlPrescription.addParam("prescription_id", prescription_id);
      urlPrescription.addParam("element_id", element_id);
      urlPrescription.addParam("category_name", category_name);
      urlPrescription.addParam("mode_protocole", mode_protocole);
      if(mode_protocole){
        urlPrescription.requestUpdate("vw_protocole", { waitingText : null });
      } else {
        urlPrescription.requestUpdate("produits_elements", { waitingText : null });
      }
  },
  reloadAddProt: function(protocole_id) {
    Prescription.reload(protocole_id, '','', '1');
    Protocole.refreshList('',protocole_id);
  },
  reloadDelProt: function(){
    Prescription.reload('', '','', '1');
  },
  reloadAlertes: function(prescription_id) {
    if(prescription_id){
      var urlAlertes = new Url;
      urlAlertes.setModuleAction("dPprescription", "httpreq_alertes_icons");
      urlAlertes.addParam("prescription_id", prescription_id);
      urlAlertes.requestUpdate("alertes", { waitingText : null });
    } else {
      alert('Pas de prescription en cours');
    }
  },
  print: function(prescription_id) {
    if(prescription_id){
      var url = new Url;
      url.setModuleAction("dPprescription", "print_prescription");
      url.addParam("prescription_id", prescription_id);
      url.popup(700, 600, "print_prescription");
    }
  },
  viewFullAlertes: function(prescription_id) {
    var url = new Url;
    url.setModuleAction("dPprescription", "vw_full_alertes");
    url.addParam("prescription_id", prescription_id);
    url.popup(700, 550, "Alertes");
  },
  onSubmitCommentaire: function(oForm, prescription_id, category_name){
    return onSubmitFormAjax(oForm, { 
      onComplete: function() { 
        Prescription.reload(prescription_id, null, category_name)
      } 
    } );
  }
};