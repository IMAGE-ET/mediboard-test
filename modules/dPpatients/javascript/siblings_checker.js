/**
 * Check for siblings or too different text
 */ 
SiblingsChecker = {
  textDifferent: null,
  textSiblings: null,
  formName: null,
  
	// Mutex
  running : false,

  // Send Ajax request
  request: function(oForm) {
	  if (this.running) {
	    return;
	  }
		  
	  this.running = true;
	  this.formName = oForm.name;
	
	  var url = new Url;
	  url.setModuleAction("dPpatients", "httpreq_get_siblings");
	  
	  url.addElement(oForm.patient_id);
	  url.addElement(oForm.nom);
	  url.addElement(oForm.prenom);
	  url.addParam("naissance", $(oForm.naissance).getFormatted("99/99/9999", "$3-$2-$1"));
	  
	  url.requestUpdate('systemMsg', { 
	  	waitingText: "Vérification des doublons"
	  } );
  },
  
  // Ask confirmation before sending, when necessary
  alert: function() {
	confirmed = true;
	confirmed &= !this.textMatching  || alert(this.textMatching);
	confirmed &= !this.textSiblings || confirm(this.textSiblings);
	
    if (confirmed) {
      document.forms[this.formName].submit();
    } 
    
    this.running = false;
  },
  
  confirm: function() {
    confirmed = true;
    confirmed &= !this.textSiblings || confirm(this.textSiblings);
    
    if (confirmed) {
      document.forms[this.formName].submit();
    }
	
    this.running = false;
  }
}

