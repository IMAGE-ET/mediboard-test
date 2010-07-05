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
    });
  },
  
  showMessage: function(){
    var db_warning = $('doublon-warning');
    var db_error   = $('doublon-error');
    var create_pat = $('submit-patient');
    
    function nl2br(str){
      return str.replace(/\n/g, "<br />").replace(/\t/g, "&nbsp;&nbsp;&nbsp;");
    }
    
    if (this.textSiblings) {
      db_warning.update(nl2br(this.textSiblings)).show();
    } else {
      db_warning.hide();  
    }
       
    if (this.textMatching) {
      db_error.update(nl2br(this.textMatching)).show();
      create_pat.disabled = true;
    } else {
      db_error.hide();
      create_pat.disabled = false;
    }
    
    this.running = false;
  },
  
  // Ask confirmation before sending, when necessary
  alert: function() {
    if (!this.submit) {
      this.showMessage();
      return;
    }

    var confirmed = true;
    confirmed &= !this.textMatching  || alert(this.textMatching);
    confirmed &= !this.textSiblings || confirm(this.textSiblings+"\nVoulez-vous tout de même sauvegarder ?");
    
    if (this.submit && confirmed) {
      document.forms[this.formName].submit();
    } 
    
    this.running = false;
  },
  
  confirm: function() {
    if (!this.submit) {
      this.showMessage();
      return;
    }
    
    var confirmed = true;
    confirmed &= !this.textSiblings || confirm(this.textSiblings+"\nVoulez-vous tout de même sauvegarder ?");
    
    if (this.submit && confirmed) {
      document.forms[this.formName].submit();
    }
     
    this.running = false;
  }
};

