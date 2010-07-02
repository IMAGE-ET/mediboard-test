/**
 * Check for siblings or too different text
 */ 
SiblingsChecker = {
  textDifferent: null,
  textSiblings: null,
  formName: null,
  
  // Mutex
  running : false,
  
  // Submit
  submit: false,

  // Send Ajax request
  request: function(oForm) {
	  if (this.running) {
	    return;
	  }
		  
	  this.running = true;
	  this.formName = oForm.name;
	
	  var url = new Url("dPpatients", "httpreq_get_siblings");
	  
	  url.addElement(oForm.patient_id);
	  url.addElement(oForm.nom);
	  url.addElement(oForm.nom_jeune_fille);
	  url.addElement(oForm.prenom);
	  url.addElement(oForm.prenom_2);
	  url.addElement(oForm.prenom_3);
	  url.addElement(oForm.prenom_4);
	  url.addParam("naissance", $(oForm.naissance).getFormatted("99/99/9999", "$3-$2-$1"));
	  
	  url.requestUpdate('systemMsg', { 
	  	waitingText: "Vérification des doublons"
	  } );
  },
  
  // Ask confirmation before sending, when necessary
  alert: function() {
    var confirmed = true;
	var db_warning = $('doublon-warning');
	var db_error   = $('doublon-error');
	var create_pat = $('create-patient');
	
    if (!this.submit) {
	  if (this.textSiblings) {
		db_warning.update(this.textSiblings.replace(/\n/g, "<br />")).show();
	  } else {
		db_warning.hide();  
	  }
		
	  if (this.textMatching) {
		db_error.update(this.textMatching.replace("\n", "<br />")).show();
		create_pat.disabled = true;
	  } else {
	    db_error.hide();
	    create_pat.disabled = false;
	  }
	  
	  this.running = false;
	  
	  return;
    }
	
    confirmed &= !this.textMatching  || alert(this.textMatching);
    confirmed &= !this.textSiblings || confirm(this.textSiblings+"\nVoulez-vous tout de même sauvegarder ?");
    
    if (this.submit && confirmed) {
      document.forms[this.formName].submit();
    } 
    
    this.running = false;
  },
  
  confirm: function() {
    var confirmed = true;
    confirmed &= !this.textSiblings || confirm(this.textSiblings+"\nVoulez-vous tout de même sauvegarder ?");
    
    if (this.submit && confirmed) {
      document.forms[this.formName].submit();
    }
	
    this.running = false;
  }
}

