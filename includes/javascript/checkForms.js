var ElementChecker = {
  aProperties : {},
  oElement    : null,
  sTypeSpec   : null,
  aSpecTypes  : Array("refMandatory", "ref", "str", "numchar",
                      "num", "bool", "enum", "date", "time",
                      "dateTime", "float", "currency", "pct",
                      "text", "html", "email", "code"),
  setProperties : function(sTypeSpec, oElement, aProperties){
    this.aProperties = aProperties;
    this.oElement    = oElement;
    this.sTypeSpec   = sTypeSpec;
  },
  checkElement : function(){
    if(sMsg = this.checkParams()){
      return sMsg;
    }
    if (this.oElement.value == "") {
      return null;
    }
    
    switch (this.sTypeSpec){
      case "refMandatory":
        sMsg = this.refMandatory();
        break;
      case "ref":
        sMsg = this.ref();
        break;
      case "str":
        sMsg = this.str();
        break;
      case "numchar":
        sMsg = this.numchar();
        break;
      case "num":
        sMsg = this.num();
        break;
      case "bool":
        sMsg = this.bool();
        break;
      case "enum":
        sMsg = this.enum();
        break;
      case "date":
        sMsg = this.date();
        break;
      case "time":
        sMsg = this.time();
        break;
      case "dateTime":
        sMsg = this.dateTime();
        break;
      case "float":
        sMsg = this.float();
        break;
      case "currency":
        sMsg = this.currency();
        break;
      case "pct":
        sMsg = this.pct();
        break;
      case "text":
        sMsg = this.text();
        break;
      case "html":
        sMsg = this.html();
        break;
      case "email":
        sMsg = this.email();
        break;
      case "code":
        sMsg = this.code();
        break;
      default:
        sMsg = "Spécification Introuvable";
    }
    return sMsg;
  },
  checkParams : function(){
    // NotNull
    if(this.aProperties["notNull"]){
      if(this.oElement.value == ""){
        return "Ne pas peut pas être vide";
      }
    }
    if(this.oElement.value == ""){
      return null;
    }
    // moreThan
    if(this.aProperties["moreThan"]){
      var sTargetElement = this.aProperties["moreThan"];
      var oTargetElement = this.oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (this.oElement.value <= oTargetElement.value) {
        return printf("'%s' n'est pas strictement supérieur à '%s'", this.oElement.value, oTargetElement.value);
      }
    }
    
    // moreEquals
    if(this.aProperties["moreEquals"]){
      var sTargetElement = this.aProperties["moreEquals"];
      var oTargetElement = this.oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (this.oElement.value < oTargetElement.value) {
        return printf("'%s' n'est pas supérieur ou égal à '%s'", this.oElement.value, oTargetElement.value);
      }
    }
    
    // sameAs
    if(this.aProperties["sameAs"]){
      var sTargetElement = this.aProperties["sameAs"];
      var oTargetElement = this.oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (this.oElement.value != oTargetElement.value) {
        var oTargetLabel = getLabelFor(oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : this.oElement.name;
        return printf("Doit être identique à %s", sTargetLabel);
      }
    }
    return null;
  }
}


Object.extend(ElementChecker, {
  // refMandatory
  refMandatory: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une référence (format non numérique)";
    }
    iElementValue = parseInt(this.oElement.value, 10);
    if (iElementValue == 0) {
      return "Ne peut pas être une référence nulle";
    }
    return this.ref();
  },
  
  // ref
  ref: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une référence (format non numérique)";
    }
    iElementValue = parseInt(this.oElement.value, 10);
    if (iElementValue < 0) {
      return "N'est pas une référence (entier négatif)";
    }

    // xor
    if(this.aProperties["xor"]){
      var sTargetElement = this.aProperties["xor"];
      if (!sTargetElement) {
        return "Spécification de chaîne de caractères invalide";
      }
      var oTargetElement = this.oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      var oTargetLabel = getLabelFor(oTargetElement);
      var oLabel = getLabelFor(this.oElement);
      var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : sTargetElement;
      var sLabel = oLabel ? oLabel.innerHTML : oElement.name;
      if (this.oElement.value == "" && oTargetElement.value == ""){
        return printf("Merci de choisir soit '%s', soit '%s'", sLabel, sTargetLabel);  
      }
      if (this.oElement.value != "" && oTargetElement.value != ""){
        return printf("Vous ne devez choisir qu'un seul de ces champs : '%s', '%s'", sLabel, sTargetLabel);   
      }
    }
    
    //  nand
    if(this.aProperties["nand"]){
      // Non implémenté
    }
    return null;
  },
  
  // str
  str: function() {
    // length
    if(this.aProperties["length"]){
      iLength = parseInt(this.aProperties["length"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length != iLength) {
        return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
      }
    }
    
    // minLength
    if(this.aProperties["minLength"]){
      iLength = parseInt(this.aProperties["minLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length < iLength) {
        return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
      }
    }
    
    // maxLength
    if(this.aProperties["maxLength"]){
      iLength = parseInt(this.aProperties["maxLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length > iLength) {
        return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
      }
    }
    return null;
  },
  
  // numchar
  numchar: function() {
    return this.num();
  },
  
  // num
  num: function() {
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
        return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
      }
    }
    
    // minLength
    if(this.aProperties["minLength"]){
      iLength = parseInt(this.aProperties["minLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length < iLength) {
        return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
      }
    }
    
    // maxLength
    if(this.aProperties["maxLength"]){
      iLength = parseInt(this.aProperties["maxLength"], 10);
      if (iLength < 1 || iLength > 255) {
        return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
      }
      if (this.oElement.value.length > iLength) {
        return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
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
  bool: function() {
    if (isNaN(this.oElement.value)) {
      return "N'est pas une chaîne numérique";
    }
    if(this.oElement.value!=0 && this.oElement.value!=1){
      return "Ne peut être différent de 0 ou 1";
    }
    return null;
  },
  
  // enum
  enum: function() {
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
  date: function() {
    if(!this.oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // time
  time: function() {
    if(!this.oElement.value.match(/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // dateTime
  dateTime: function() {
    if(!this.oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})[ \+](\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
      return "N'as pas un format correct";
    }
    return null;
  },
  
  // float
  float: function() {
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
  currency: function() {
    return this.float();
  },
  
  // pct
  pct: function() {
    if (!this.oElement.value.match(/^(\d+)(\.\d{1,2})?$/)) {
      return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
    }
    return null;
  },
  
  // text
  text: function() {
    return null;
  },
  
  // html
  html: function() {
    return null;
  },
  
  // email
  email: function() {
    if (!this.oElement.value.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
      return "Le format de l'email n'est pas valide";
    }
    return null;
  },
  
  // code
  code: function() {
    // ccam
    if(this.aProperties["ccam"]){
      if (!this.oElement.value.match(/^([a-z]){4}([0-9]){3}$/i)) {
        return "Code CCAM incorrect, doit contenir 4 lettres et 3 chiffres";
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
    }else if(this.aProperties["insee"]){
      aMatches = this.oElement.value.match(/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i);
      if (!aMatches) {
        return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
      }
      nCode = parseInt(aMatches[1], 10);
      nCle  = parseInt(aMatches[2], 10);
      if (97 - (nCode % 97) != nCle) {
        return "Matricule incorrect, la clé n'est pas valide";
      }
    
    // Aucune Specification
    }else{
      return "Spécification de code invalide";
    }
    return null;
  }
} );


/***************/


function checkForm(oForm){
  var oElementFirstFailed = null;
  var aMsgFailed = new Array;
  var iElement = 0;
  while (oElement = oForm.elements[iElement++]) {
    var aSpecFragments = null;
    if(sPropSpec = oElement.getAttribute("title")){
      aSpecFragments = sPropSpec.split(" ");
    }else if(sPropSpec = oElement.getAttribute("class")){
      aSpecFragments = sPropSpec.split(" ");
    }  
    
    if (aSpecFragments) {
      var oLabel      = getLabelFor(oElement);
      var aMsg        = new Array;
      var aProperties = {};
      var sTypeName   = null;
      
      aSpecFragments.each(function (value) {
        if(ElementChecker.aSpecTypes.indexOf(value) != -1){
          sTypeName = value;
        }else{
          aParams = value.split("|");
          if(aParams.length == 1){
            aProperties[value] = true;
          }else{
            key = aParams.shift();
            aProperties[key] = aParams.join("|");
          }
        }
      });
      if(sTypeName){
        // Type de spec trouvé
        ElementChecker.setProperties(sTypeName, oElement, aProperties);
        if(sMsg = ElementChecker.checkElement()){
          aMsg.push("\n => " + sMsg);
        }
        
        if(aMsg.length != 0){
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
  }
  if (aMsgFailed.length) {
    var sMsg = "Merci de remplir/corriger les champs suivants : \n";
    sMsg += aMsgFailed.join("\n")
    alert(sMsg);
    
    if (oElementFirstFailed) {
      if (oElementFirstFailed.type != "hidden") {
        oElementFirstFailed.focus();
      }
      var oDoubleClick = oElementFirstFailed["ondblclick"] || Prototype.emptyFunction;
      oDoubleClick();
    }
    return false;
  }
  
  return true;
}