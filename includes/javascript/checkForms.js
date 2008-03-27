var ElementChecker = {
  aProperties    : {},
  oElement       : null,
  sTypeSpec      : null,
  oTargetElement : null,
  oCompare       : null,
  aSpecTypes     : Array("ref", "str", "numchar", "num", 
                      "bool", "enum", "date", "time",
                      "dateTime", "float", "currency", "pct",
                      "text", "html", "email", "url", "code", "password"),
  
  prepare : function(oElement){
    //Initialisation
    var aSpecFragments = null;
    this.aProperties  = {};
    this.oElement     = oElement;
    this.sTypeSpec    = null;
    // Extraction des props
    if(sPropSpec = oElement.title){
      aSpecFragments = sPropSpec.split(" ");
    }else if(sPropSpec = oElement.className){
      aSpecFragments = sPropSpec.split(" ");
    }
    
    // Props trouvées : Recherche de la prop principale et creation propriétés
    if(aSpecFragments){
      aSpecFragments.each(function (value) {
        if(ElementChecker.aSpecTypes.indexOf(value) != -1){
          ElementChecker.sTypeSpec = value;
        }else{
          aParams = value.split("|");
          if(aParams.length == 1){
            ElementChecker.aProperties[value] = true;
          }else{
            key = aParams.shift();
            ElementChecker.aProperties[key] = aParams.join("|");
          }
        }
      });
    }
  },
  
  checkElement : function(){
    if(sMsg = this.checkParams()){
      return sMsg;
    }
    if (this.oElement.value == "") {
      return null;
    }
    
    sMsg = this["check_" + this.sTypeSpec]();
    
    // Free DOM element references
    this.oElement = null;
    this.oTargetElement = null;
    this.oCompare = null;

    return sMsg;
  },
  
  getCastFunction: function() {
    switch (this.sTypeSpec) {
    	case "num": return function(value) { return parseInt(value, 10); }
			case "float": return function(value) { return parseFloat(value, 10); }
			case "date": return function(value) { return Date.fromDATE(value); }
    	default : return Prototype.K;
    }
  },
  
  castCompareValues: function(sTargetElement) {
    this.oTargetElement = this.oElement.form.elements[sTargetElement];
    if (!this.oTargetElement) {
      return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
    }
    
    var fCaster = this.getCastFunction();
  	
  	this.oCompare = {
      source : this.oElement.value ? fCaster(this.oElement.value) : null,
      target : this.oTargetElement.value ? fCaster(this.oTargetElement.value) : null
  	}

  	return null;
  },
  
  checkParams : function(){
    // NotNull
    if(this.aProperties["notNull"]){
      if(this.oElement.value == ""){
        return "Ne pas peut pas être vide";
      }
    }
    
    // xor
    if(this.aProperties["xor"]){
      var oLabel = getLabelFor(this.oElement);
      var sLabel = oLabel ? oLabel.innerHTML : oElement.name;
      var iNbElements = this.oElement.value != "";
      var sListElements = sLabel;
      var message = "";
      this.aProperties["xor"].split("|").each(function(sTargetElement) {
        if (!sTargetElement) {
          message += "Spécification de chaîne de caractères invalide";
        }
        var oTargetElement = this.oElement.form.elements[sTargetElement];
        if (!oTargetElement) {
          message += printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
        } else {
          var oTargetLabel = getLabelFor(oTargetElement);
          var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : sTargetElement;
          iNbElements += (oTargetElement.value != "");
          sListElements += ", " + sTargetLabel;
        }
      });
      if(message != "") {
        return message;
      }
      if (iNbElements != 1){
        return printf("Vous devez choisir une et une seule de valeur entre '%s", sListElements);  
      }
    }
    
    if(this.oElement.value == ""){
      return null;
    }
    
    var sTargetElement = null;
    var sParamMsg = null;
    
    // moreThan
    if (sTargetElement = this.aProperties["moreThan"]) {
    	if (sParamMsg = this.castCompareValues(sTargetElement)) {
    		return sParamMsg;
    	}
    	
      if (this.oCompare.source <= this.oCompare.target) {
        return printf("'%s' n'est pas strictement supérieur à '%s'", this.oElement.value,  this.oTargetElement.value);
      }
    }
    
    // moreEquals
    if (sTargetElement = this.aProperties["moreEquals"]) {
    	if (sParamMsg = this.castCompareValues(sTargetElement)) {
    		return sParamMsg;
    	}
    	
      if (this.oCompare.source < this.oCompare.target) {
        return printf("'%s' n'est pas supérieur ou égal à '%s'", this.oElement.value,  this.oTargetElement.value);
      }
    }

    // sameAs
    if (sTargetElement = this.aProperties["sameAs"]) {
    	if (sParamMsg = this.castCompareValues(sTargetElement)) {
    		return sParamMsg;
    	}
    	
      if (this.oCompare.source != this.oCompare.target) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : this.oElement.name;
        return printf("Doit être identique à %s", sTargetLabel);
      }
    }
    
    // notContaining
    if (sTargetElement = this.aProperties["notContaining"]) {
    	if (sParamMsg = this.castCompareValues(sTargetElement)) {
    		return sParamMsg;
    	}
    	
      if (this.oCompare.source.match(this.oCompare.target)) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        return printf("Ne doit pas contenir %s", sTargetLabel);
      }
    }
    
    // notNear
    if (sTargetElement = this.aProperties["notNear"]) {
      if (sParamMsg = this.castCompareValues(sTargetElement)) {
        return sParamMsg;
      }
      
      if (levenshtein(this.oCompare.target, this.oCompare.source) < 3) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        return printf("Ressemble trop à %s", sTargetLabel);
      }
    }
    
    // alphaAndNum
   	if(this.aProperties["alphaAndNum"]){
	  if (!this.oElement.value.match(/[a-z]/)) {
	    return "Doit contenir au moins une lettre";
	  }
	  if (!this.oElement.value.match(/\d+/)) {
		return "Doit contenir au moins un chiffre";
	  }
	}
    
    return null;
  }
}


Object.extend(ElementChecker, {  
  // ref
  check_ref: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une référence (format non numérique)";
    }
    iElementValue = parseInt(this.oElement.value, 10);
    if (iElementValue == 0) {
      return "Ne peut pas être une référence nulle";
    }
    if (iElementValue < 0) {
      return "N'est pas une référence (entier négatif)";
    }
    return null;
  },
  
  // str
  check_str: function() {
    // length
    if(this.aProperties["length"]){
      iLength = parseInt(this.aProperties["length"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length != iLength) {
        return printf("N'a pas la bonne longueur (longueur souhaitée : %s)", iLength);
      }
    }
    
    // minLength
    if(this.aProperties["minLength"]){
      iLength = parseInt(this.aProperties["minLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length < iLength) {
        return printf("N'a pas la bonne longueur (longueur minimale souhaitée : %s)", iLength);
      }
    }
    
    // maxLength
    if(this.aProperties["maxLength"]){
      iLength = parseInt(this.aProperties["maxLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length > iLength) {
        return printf("N'a pas la bonne longueur (longueur maximale souhaitée : %s)", iLength);
      }
    }
    return null;
  },
  
  // numchar
  check_numchar: function() {
    return this.check_num();
  },
  
  // num
  check_num: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une chaîne numérique";
    }
    
    // pos
    if(this.aProperties["pos"]){
      if (this.oElement.value <= 0) {
        return "Doit avoir une valeur positive";
      }
    }
    
    // min
    if(this.aProperties["min"]){
      iMin = parseInt(this.aProperties["min"], 10);
      if (iMin == NaN) {
        return "Spécification de minimum numérique invalide";
      }
      if (this.oElement.value < iMin) {
        return printf("Soit avoir une valeur minimale de %s", iMin);
      }
    }
    
    // max
    if(this.aProperties["max"]){
      iMax = parseInt(this.aProperties["max"], 10);
      if (iMax == NaN) {
        return "Spécification de maximum numérique invalide";
      }
      if (this.oElement.value > iMax) {
        return printf("Soit avoir une valeur maximale de %s", iMin);
      }
    }
    
    // length
    if(this.aProperties["length"]){
      iLength = parseInt(this.aProperties["length"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length != iLength) {
        return printf("N'a pas la bonne longueur (longueur souhaitée : %s)", iLength);
      }
    }
    
    // minLength
    if(this.aProperties["minLength"]){
      iLength = parseInt(this.aProperties["minLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length < iLength) {
        return printf("N'a pas la bonne longueur (longueur minimale souhaitée : %s)", iLength);
      }
    }
    
    // maxLength
    if(this.aProperties["maxLength"]){
      iLength = parseInt(this.aProperties["maxLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length > iLength) {
        return printf("N'a pas la bonne longueur (longueur maximale souhaitée : %s)", iLength);
      }
    }
    
    // minMax
    if(this.aProperties["minMax"]){
      aSpecFragments = this.aProperties["minMax"].split("|");
      var iMin = parseInt(aSpecFragments[0], 10);
      var iMax = parseInt(aSpecFragments[1], 10);
      if (this.oElement.value > iMax || this.oElement.value < iMin) {
        return printf("N'est pas compris entre %i et %i", iMin, iMax);
      }
    }
    return null;
  },
  
  // bool
  check_bool: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une chaîne numérique";
    }
    if(this.oElement.value!=0 && this.oElement.value!=1){
      return "Ne peut être différent de 0 ou 1";
    }
    return null;
  },
  
  // enum
  check_enum: function() {
    if(!this.aProperties["list"]){
      return "Spécification 'list' manquante pour le champ " + this.oElement.name;
    }
    aSpecFragments = this.aProperties["list"].split("|");
    if (!aSpecFragments.contains(this.oElement.value)) {
      return "N'est pas une valeur possible";
    }
    return null;
  },
  
  // date
  check_date: function() {
    if(!this.oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // time
  check_time: function() {
    if(!this.oElement.value.match(/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // dateTime
  check_dateTime: function() {
    if(!this.oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})[ \+](\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // float
  check_float: function() {
    if(isNaN(parseFloat(this.oElement.value)) || parseFloat(this.oElement.value)!=this.oElement.value){
      return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
    }
    
    // pos
    if(this.aProperties["pos"]){
      if (this.oElement.value <= 0) {
        return "Doit avoir une valeur positive";
      }
    }
    
    // min
    if(this.aProperties["min"]){
      iMin = parseFloat(this.aProperties["min"], 10);
      if (iMin == NaN) {
        return "Spécification de minimum numérique invalide";
      }
      if (this.oElement.value < iMin) {
        return printf("Soit avoir une valeur minimale de %s", iMin);
      }
    }
    
    // max
    if(this.aProperties["max"]){
      iMax = parseFloat(this.aProperties["max"], 10);
      if (iMax == NaN) {
        return "Spécification de maximum numérique invalide";
      }
      if (this.oElement.value > iMax) {
        return printf("Soit avoir une valeur maximale de %s", iMin);
      }
    }
    
    // minMax
    if(this.aProperties["minMax"]){
      aSpecFragments = this.aProperties["minMax"].split("|");
      var iMin = parseInt(aSpecFragments[0], 10);
      var iMax = parseInt(aSpecFragments[1], 10);
      if (this.oElement.value > iMax || this.oElement.value < iMin) {
        return printf("N'est pas compris entre %i et %i", iMin, iMax);
      }
    }
    return null;
  },
  
  // currency
  check_currency: function() {
    return this.check_float();
  },
  
  // pct
  check_pct: function() {
    if (!this.oElement.value.match(/^(\d+)(\.\d{1,4})?$/)) {
      return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
    }
    return null;
  },
  
  // text
  check_text: function() {
    return null;
  },
  
  // html
  check_html: function() {
    return null;
  },
  
//(http|https|ftp)?(www\.)?([\w*])\.[a-zA-Z]{2,3}[/]?$
  check_url: function() {
	  var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	  if (!this.oElement.value.match(regexp)) {
       return "Le format de l'url n'est pas valide";
    }
    return null;
  },
  
  
  
  // email
  check_email: function() {
    if (!this.oElement.value.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
      return "Le format de l'email n'est pas valide";
    }
    return null;
  },
  
  // code
  check_code: function() {
    // ccam
    if(this.aProperties["ccam"]){
      if (!this.oElement.value.match(/^([A-Z]){4}[0-9]{3}(-[0-9](-[0-9])?)?$/i)) {
        return "Code CCAM incorrect";
      }
    
    // cim10
    }else if(this.aProperties["cim10"]){
      if (!this.oElement.value.match(/^([a-z0-9]){0,5}$/i)) {
        return "Code CCAM incorrect, doit contenir 5 lettres maximum";
      }
      
    // adeli
    }else if(this.aProperties["adeli"]){
      if (!this.oElement.value.match("/^([0-9]){9}$/i")) {
        return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
      }
      
    // insee
    }else if(this.aProperties["insee"]) {
    
    	if (this.oElement.value.match(/^([0-9]{7,8}[A-Z])$/i)) {
    	  return null;
    	}
    	
      if (aMatches = this.oElement.value.match(/^([1278][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i)) {
	      nCode = parseInt(aMatches[1], 10);
	      nCle  = parseInt(aMatches[2], 10);
	      if (97 - (nCode % 97) != nCle) {
	        return "Matricule incorrect, la clé n'est pas valide";
	      }
	      
	      return null;
      }

			return "Matricule incorrect";
      
    // Aucune Specification
    }else{
      return "Spécification de code invalide";
    }
    return null;
  },
  
    // password
  check_password: function() {
  	// TODO: Factoriser le minLength et les autres
    if(this.aProperties["minLength"]){
    	iLength = parseInt(this.aProperties["minLength"]);
    	
	    // length
	    if (this.oElement.value.length < iLength) {
	    	return printf("N'a pas la bonne longueur (longueur minimale souhaitée : %s)", iLength);
	    }
	}
    return null;
  }
} );


/***************/


function checkForm(oForm) {
  var oElementFirstFailed = null;
  var aMsgFailed = new Array;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    var aMsg   = new Array;
    ElementChecker.prepare(oElement);
    
    if (ElementChecker.sTypeSpec) {
      // Type de spec trouvé
      var oLabel = getLabelFor(oElement);
      
      if (sMsg = ElementChecker.checkElement()) {
        aMsg.push("\n    - " + sMsg);
      }
      
      if (aMsg.length != 0) {
        var sLabelTitle = oLabel ? oLabel.getAttribute("title") : null;
        var sMsgFailed = sLabelTitle ? sLabelTitle : printf("%s (val:'%s', spec:'%s')", oElement.name, oElement.value, sPropSpec);
        sMsgFailed += aMsg.join("");
        aMsgFailed.push("- " + sMsgFailed);
        
        if (!oElementFirstFailed) {
          oElementFirstFailed = oElement;
        }
      }
      if (oLabel) {
        oLabel.style.color = aMsg.length ? "#f00" : "#000";
      }
    }
  }
  
  if (aMsgFailed.length) {
    var sMsg = "Merci de remplir/corriger les champs suivants : \n";
    sMsg += aMsgFailed.join("\n")
    alert(sMsg);
    
    if (oElementFirstFailed) {
      if (oElementFirstFailed.type != "hidden") {
        try {
          oElementFirstFailed.focus();
        }
        catch(e){}
      }
      var oDoubleClick = oElementFirstFailed["ondblclick"] || Prototype.emptyFunction;
      oDoubleClick();
    }
    return false;
  }
  FormObserver.changes = 0;
  return true;
}

/** Validation d'un element de formulaire. 
  * Est utile pour la validation lors de la saisie du formulaire.
  */
function checkFormElement(oElement) {
	ElementChecker.prepare(oElement);
	
	// Recuperation de l'element HTML qui accueillera le message.
	var oMsg = $(oElement.name+'_message');
	
	if (ElementChecker.sTypeSpec) {
		switch (ElementChecker.sTypeSpec) {
		case 'password':
			if (oMsg) {
				if (ElementChecker.checkElement()) {
					oMsg.innerHTML = 'Sécurité trop faible';
					oMsg.style.backgroundColor = '#FF7A7A';
				} else {
					oMsg.innerHTML = 'Sécurité correcte';
					oMsg.style.backgroundColor = '#33FF66';
				}
			}
			break;
			
		default:
		}
	}
	if (oElement.value == '') {
		oMsg.innerHTML = '';
		oMsg.style.background = 'none';
	}
	return true;
}
