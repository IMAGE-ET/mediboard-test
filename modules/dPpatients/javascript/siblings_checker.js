/**
 * Check for siblings or too different text
 */ 
SiblingsChecker = {
  textDifferent: null,
  textSiblings: null,
  
	// Mutex
  running : false,

  // Send Ajax request
  request: function() {
	  if (this.running) {
	    return;
	  }
	  
	  this.running = true;
	  
  	var oForm = document.editFrm;

	  var url = new Url;
	  url.setModuleAction("dPpatients", "httpreq_get_siblings");
	  url.addElement(oForm.patient_id);
	  url.addElement(oForm.nom);
	  url.addElement(oForm.prenom);
	
	  if (oForm._annee.value != "" && oForm._mois.value != "" && oForm._jour.value != "") {
	    url.addParam("naissance", oForm._annee.value + "-" + oForm._mois.value + "-" + oForm._jour.value);
	  }
	  
	  url.requestUpdate('systemMsg', { 
	  	waitingText: "Vérification des doublons"
	  } );
  },
  
  // Ask confirmation before sending, when necessary
  confirm: function() {
    confirmed = true;
    confirmed &= !this.textDifferent || confirm(this.textDifferent);
    confirmed &= !this.textSiblings  || confirm(this.textSiblings);
    if (confirmed) {
      document.editFrm.submit();
    }
	  this.running = false;
  }
}

