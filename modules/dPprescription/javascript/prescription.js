var Prescription = {
	// Multiples occurences de la même widget
  suffixes: [],
  addEquivalent: function(code, line_id, mode_pharma){
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_substitute_line");
    url.addParam("code_cip", code);
    url.addParam("line_id", line_id);
    url.addParam("mode_pharma", mode_pharma);
    url.requestUpdate("systemMsg", { waitingText : null });
    /*
    Prescription.delLineWithoutRefresh(line_id);
    // Suppression des champs de addLine
    var oForm = document.addLine;
    oForm.prescription_line_medicament_id.value = "";
    oForm.del.value = "";
    Prescription.addLine(code);
    */
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
    
    var oFormDate = document.selDateLine;
    if(oFormDate && oFormDate.debut.value){
      oForm.debut.value = oFormDate.debut.value;  
    }
  
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
  addLineElement: function(element_id, chapitre, debut, duree, unite_duree, callback){
    // Formulaire contenant la categorie courante
    var oForm = document.addLineElement;
    
    var oFormDate = document.selDateLine;
    if(oFormDate && oFormDate.debut.value){
      oForm.debut.value = oFormDate.debut.value;  
    }
    
    if(debut){
      oForm.debut.value = debut;
    }
    if(duree && unite_duree){
      oForm.duree.value = duree;
      oForm.unite_duree.value = unite_duree;
    }
    if(callback){
      oForm.callback.value = callback;
    }
    if(!chapitre){
      var chapitre = oForm._chapitre.value;
    }
    oForm.element_prescription_id.value = element_id;
   
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function(){ 
        if(!callback){
          Prescription.reload(oForm.prescription_id.value, element_id, chapitre);
        }
      }
    });
    oForm.debut.value = "";
    oForm.duree.value = "";
    oForm.unite_duree.value = "";
    oForm.callback.value = "";
  },



  submitPriseElement: function(element_id){
    var oFormElement = document.addLineElement;
    var oForm = document.addPriseElement;
    oForm.object_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function(){
        Prescription.reload(oFormElement.prescription_id.value, element_id, oForm.chapitre.value);
      } 
    });
  },
  
  submitPriseElementWithoutRefresh: function(element_id){
    var oFormElement = document.addLineElement;
    var oForm = document.addPriseElement;
    oForm.object_id.value = element_id;
    submitFormAjax(oForm, 'systemMsg');
  },
  


  addLineElementWithoutRefresh: function(element_id, debut, duree, unite_duree, callback){
    // Formulaire contenant la categorie courante
    var oForm = document.addLineElement;
    if(debut){
      oForm.debut.value = debut;
    }
    if(duree && unite_duree){
      oForm.duree.value = duree;
      oForm.unite_duree.value = unite_duree;
    }
    if(callback){
      oForm.callback.value = callback;
    }
    oForm.element_prescription_id.value = element_id;
    
    submitFormAjax(oForm, 'systemMsg');
    
    oForm.debut.value = "";
    oForm.duree.value = "";
    oForm.unite_duree.value = "";
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
  delLineElement: function(line_id, chapitre) {
    var oForm = document.addLineElement;
    oForm.prescription_line_element_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){ 
        Prescription.reload(oForm.prescription_id.value, null, chapitre);
      } 
    });
  },
  stopTraitementPerso: function(oSelect, prescription_id, mode_pharma) {
    $V(oSelect, "");
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_prescription_modif_all_tp");
    url.addParam("prescription_id", prescription_id);
    url.addParam("actionType", "stop");
    if(document.selDateLine){
      url.addParam("date", $V(document.selDateLine.debut));
    }
    url.addParam("mode_pharma", mode_pharma);
    url.requestUpdate("systemMsg", { waitingText : null });
  },
  goTraitementPerso: function(oSelect, prescription_id, mode_pharma) {
    $V(oSelect, "");
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_prescription_modif_all_tp");
    url.addParam("prescription_id", prescription_id);
    url.addParam("actionType", "go");
    if(document.selDateLine){
      url.addParam("date", $V(document.selDateLine.debut));
    }
    if(document.selPraticienLine) {
      url.addParam("praticien_id", $V(document.selPraticienLine.praticien_id));
    }
    url.addParam("mode_pharma", mode_pharma);
    url.requestUpdate("systemMsg", { waitingText : null });
  },
  reload: function(prescription_id, element_id, chapitre, mode_protocole,mode_pharma,line_id) {

      // Select de choix du praticien
      if(document.selSortie){
        var praticien_sortie_id = document.selSortie.selPraticien.value;
      }
  
      var oForm = document.addLine;    
      if(window.opener){
        window.opener.PrescriptionEditor.refresh(oForm.object_id.value, oForm.object_class.value);
      }
      var urlPrescription = new Url;
      urlPrescription.setModuleAction("dPprescription", "httpreq_vw_prescription");
      urlPrescription.addParam("prescription_id", prescription_id);
      urlPrescription.addParam("element_id", element_id);
      urlPrescription.addParam("chapitre", chapitre);
      urlPrescription.addParam("mode_protocole", mode_protocole);
      urlPrescription.addParam("mode_pharma", mode_pharma);
      urlPrescription.addParam("praticien_sortie_id", praticien_sortie_id);
     
      if(mode_pharma == "1"){
          urlPrescription.requestUpdate("div_medicament", { waitingText : null, onComplete: function(){ Prescription.testPharma(line_id) } });      
      } else {
	      if(mode_protocole){
	        urlPrescription.requestUpdate("vw_protocole", { waitingText : null });
	      } else {
	        if(chapitre){
	          urlPrescription.requestUpdate("div_"+chapitre, { waitingText: null } );     
	        } else {
	         // Dans le cas de la selection d'un protocole, rafraichissement de toute la prescription
	         urlPrescription.requestUpdate("produits_elements", { waitingText: null } );
	        }
	      }
      }
  },
  testPharma: function(line_id){  
    if(line_id){
	    var oFormAccordPraticien = document.forms["editLineAccordPraticien-"+line_id];
	    if(oFormAccordPraticien.accord_praticien.value == 0){
	      if(confirm("Modifiez vous cette ligne en accord avec le praticien ?")){
	        oFormAccordPraticien.__accord_praticien.checked = true;
	        $V(oFormAccordPraticien.accord_praticien,"1");
	      }
	    }
    }
  },
  reloadPrescSejour: function(prescription_id, sejour_id, praticien_sortie_id){
    var url = new Url;
    url.setModuleAction("dPprescription", "httpreq_vw_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("sejour_id", sejour_id);
    url.addParam("full_mode", "1");
    url.addParam("praticien_sortie_id", praticien_sortie_id);
    url.requestUpdate("prescription_sejour", { waitingText: null } );
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
  printPrescription: function(prescription_id, ordonnance,print) {
    // Select de choix du praticien
    var praticien_sortie_id = "";
    if(document.selSortie){
      var praticien_sortie_id = document.selSortie.selPraticien.value;
    }
      
    if(prescription_id){
      var url = new Url;
      url.setModuleAction("dPprescription", "print_prescription");
      url.addParam("prescription_id", prescription_id);
      url.addParam("praticien_sortie_id", praticien_sortie_id);
      url.addParam("print", print);
      if(ordonnance){
        url.addParam("ordonnance", ordonnance);
      }
      url.popup(800, 600, "print_prescription");
    }
  },
  viewFullAlertes: function(prescription_id) {
    var url = new Url;
    url.setModuleAction("dPprescription", "vw_full_alertes");
    url.addParam("prescription_id", prescription_id);
    url.popup(700, 550, "Alertes");
  },
  onSubmitCommentaire: function(oForm, prescription_id, chapitre){
    return onSubmitFormAjax(oForm, { 
      onComplete: function() { 
        Prescription.reload(prescription_id, null, chapitre)
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
  },
  viewProduit: function(cip){
    var url = new Url;
    url.setModuleAction("dPmedicament", "vw_produit");
    url.addParam("CIP", cip);
    url.popup(900, 640, "Descriptif produit");
  },
  viewHistorique: function(prescription_id, type){
	  var url = new Url;
	  url.setModuleAction("dPprescription", "view_historique");
	  url.addParam("prescription_id", prescription_id);
	  url.addParam("type", type);
	  url.popup(500, 400, type);
  }
};


