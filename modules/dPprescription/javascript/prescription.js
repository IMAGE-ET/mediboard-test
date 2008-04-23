var Prescription = {
	// Multiples occurences de la même widget
  suffixes: [],
  addEquivalent: function(code, line_id){
    Prescription.delLineWithoutRefresh(line_id);
    // Suppression des champs de addLine
    var oForm = document.addLine;
    oForm.prescription_line_medicament_id.value = "";
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
    
    var mode_pharma = oForm.mode_pharma.value;
    var oFormTraitement = document.transfertToTraitement;
    if(oForm.del.value == 0 && oFormTraitement._type.value == "pre_admission" && oFormTraitement.object_id.value != ""){
      submitFormAjax(oForm, 'systemMsg');
    } else {
      submitFormAjax(oForm, 'systemMsg', { 
	      onComplete : 
	        function(){
	          Prescription.reload(oForm.prescription_id.value, '', 'medicament','',mode_pharma);
	        } 
	    });
    }
  },
  addLineElement: function(element_id, category_name){
    // Formulaire contenant la categorie courante
    var oForm = document.addLineElement;
    if(!category_name){
    var category_name = oForm._category_name.value;
    }
    oForm.element_prescription_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function(){ 
        Prescription.reload(oForm.prescription_id.value, element_id, category_name);
       }
     });
  },
  delLineWithoutRefresh: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_medicament_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg');
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_medicament_id.value = line_id;
    oForm.del.value = 1;
    var mode_pharma = oForm.mode_pharma.value;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ 
        Prescription.reload(oForm.prescription_id.value, '', 'medicament','',mode_pharma);
       } 
    });
  },
  delLineElement: function(line_id, category_name) {
    var oForm = document.addLineElement;
    oForm.prescription_line_element_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ 
        Prescription.reload(oForm.prescription_id.value, null, category_name);
      } 
    });
  },
  reload: function(prescription_id, element_id, category_name, mode_protocole,mode_pharma) {
      var oForm = document.addLine;
      
      if(window.opener){
        window.opener.PrescriptionEditor.refresh(oForm.prescription_id.value, oForm.object_class.value);
      }
      
      var urlPrescription = new Url;
      urlPrescription.setModuleAction("dPprescription", "httpreq_vw_prescription");
      urlPrescription.addParam("prescription_id", prescription_id);
      urlPrescription.addParam("element_id", element_id);
      urlPrescription.addParam("category_name", category_name);
      urlPrescription.addParam("mode_protocole", mode_protocole);
      urlPrescription.addParam("mode_pharma", mode_pharma);
      
      
      if(mode_pharma == "1"){
    
          urlPrescription.requestUpdate("div_medicament", { waitingText : null });  
      } else {
        
	    
	      if(mode_protocole){
	        urlPrescription.requestUpdate("vw_protocole", { waitingText : null });
	      } else {
	        if(category_name){
	          urlPrescription.requestUpdate("div_"+category_name, { waitingText: null } );
	        } else {
	         // Dans le cas de la selection d'un protocole, rafraichissement de toute la prescription
	         urlPrescription.requestUpdate("produits_elements", { waitingText: null } );
	        }
	      }
      }
  },
  reloadPrescPharma: function(prescription_id){
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_vw_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("mode_pharma", "1");
    url.addParam("refresh_pharma", "1");
    url.addParam("mode_protocole", "0");
    url.requestUpdate("prescription_pharma", { waitingText: null } );
  },
  reloadAddProt: function(protocole_id) {
    Prescription.reload(protocole_id, '','', '1','0');
    Protocole.refreshList('',protocole_id);
  },
  reloadDelProt: function(){
    Prescription.reload('', '','', '1','0');
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
  printPrescription: function(prescription_id, ordonnance) {
    if(prescription_id){
      var url = new Url;
      url.setModuleAction("dPprescription", "print_prescription");
      url.addParam("prescription_id", prescription_id);
      if(ordonnance){
        url.addParam("ordonnance", ordonnance);
      }
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
  },
  refreshTabHeader: function(tabName, lineCount){
    // On cible le bon a href
    tab = $$("ul li a[href=#"+tabName+"]");
    
    // On recupere le nom de l'onglet
    tabSplit = tab[0].innerHTML.split(" ");
    name_tab = tabSplit[0];
    
    // Si le nombre de ligne est > 0
    if(lineCount > 0){
    tab[0].innerHTML = name_tab+" ("+lineCount+")";
    } else {
      tab[0].innerHTML = name_tab;
    }
  },
  submitFormStop: function(oForm, object_id, object_class){
    submitFormAjax(oForm, 'systemMsg', { onComplete: function(){ 
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_vw_stop_line");
      url.addParam("object_id", object_id);
      url.addParam("object_class", object_class)
      url.requestUpdate("stop-"+object_class+"-"+object_id,  { waitingText: null } );
    } } );
  },
  viewAllergies: function(prescription_id){
    url = new Url;
    url.setModuleAction("dPprescription", "httpreq_vw_allergies_sejour");
    url.addParam("prescription_id", prescription_id);
    url.popup(500, 300, "Allergies");
  }
};