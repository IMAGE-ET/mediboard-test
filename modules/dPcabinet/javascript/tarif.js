Tarif = {
  chir_id: null,
	function_id: null,
	
	updateTotal: function () {
	  var form = getForm("editFrm");
	  if (!form.secteur1 || !form.secteur1) {
	    return;
	  }
	  
	  var secteur1 = form.secteur1.value;
	  var secteur2 = form.secteur2.value; 
	  
	  if (secteur1 == ""){
	    secteur1 = 0;
	  }
	  
	  if (secteur2 == ""){
	    secteur2 = 0;
	  }
	  
	  form._somme.value = parseFloat(secteur1) + parseFloat(secteur2);
	  form._somme.value = Math.round(form._somme.value*100)/100;
	},
	
	updateSecteur2: function() {
	  var form = getForm("editFrm");
	  var secteur1 = form.secteur1.value;
	  var somme = form._somme.value;
		
	  if (somme == "") {
	    somme = 0;
	  }
		
	  if (secteur1 == "") {
	    secteur = 0;
	  }
		
	  form.secteur2.value = parseFloat(somme) - parseFloat(secteur1); 
	  form.secteur2.value = Math.round(form.secteur2.value*100)/100;
	},
	
	updateOwner: function() {
	  var form = getForm("editFrm");
    var type = $V(form._type);
				
	  if (type == "chir") {
	    $V(form.chir_id, this.chir_id);
	    $V(form.function_id, "");
	  }
		 
    if (type == "function") {
	    $V(form.chir_id, "");
	    $V(form.function_id, this.function_id);
	  }
	},
	
	forceRecompute: function() {
		$("force-recompute").show();
    var form = getForm("editFrm");
    form.save.disabled = true;
	}
}