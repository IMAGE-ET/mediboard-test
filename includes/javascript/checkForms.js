function checkElement(oElement, aSpecFragments) {
  // Parametres du champs
  switch (aSpecFragments[0]) {
    case "notNull":
      if(oElement.value == ""){
        return "Ne pas peut pas être vide";
      }
      return null;
      break;
    case "moreThan":
      
      var sTargetElement = aSpecFragments[1];
      var oTargetElement = oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (oElement.value <= oTargetElement.value) {
        return printf("'%s' n'est pas strictement supérieur à '%s'", oElement.value, oTargetElement.value);
      }
      return null;
      break;
      
    case "moreEquals":
      var sTargetElement = aSpecFragments[1];
      var oTargetElement = oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (oElement.value < oTargetElement.value) {
        return printf("'%s' n'est pas supérieur ou égal à '%s'", oElement.value, oTargetElement.value);
      }
      return null;
      break;
      
    case "sameAs":
      var sTargetElement = aSpecFragments[1];
      var oTargetElement = oElement.form.elements[sTargetElement];
      if (!oTargetElement) {
        return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
      }
      if (oElement.value != oTargetElement.value) {
        var oTargetLabel = getLabelFor(oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : oElement.name;
        return printf("Doit être identique à %s", sTargetLabel);
      }
      return null;
      break;
      
    case "confidential":
      return null;
      break;
  }
  
  if (oElement.value == "") {
    return null;
  }
  
  // Types du champs
  switch (aSpecFragments[0]) {      
  case "refMandatory":
    if (isNaN(oElement.value)) {
      return "N'est pas une référence (format non numérique)";
    }

    iElementValue = parseInt(oElement.value, 10);
    if (iElementValue == 0) {
      return "Ne peut pas être une référence nulle";
    }
  case "ref":
    if (isNaN(oElement.value)) {
      return "N'est pas une référence (format non numérique)";
    }

    iElementValue = parseInt(oElement.value, 10);

      if (iElementValue < 0) {
        return "N'est pas une référence (entier négatif)";
      }
      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {

          case "xor" :
          // Test existance parametre supplementaire
            var sTargetElement = aSpecFragments[2];
            
            if (!sTargetElement) {
              return "Spécification de chaîne de caractères invalide";
            }
            
          var oTargetElement = oElement.form.elements[sTargetElement];
            
          if (!oTargetElement) {
              return printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
          }
          
          var oTargetLabel = getLabelFor(oTargetElement);
          var oLabel = getLabelFor(oElement);
          var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : sTargetElement;
          var sLabel = oLabel ? oLabel.innerHTML : oElement.name;
          
            if (oElement.value == "" && oTargetElement.value == ""){
            return printf("Merci de choisir soit '%s', soit '%s'", sLabel, sTargetLabel);  
          }
              
            if (oElement.value != "" && oTargetElement.value != ""){
            return printf("Vous ne devez choisir qu'un seul de ces champs : '%s', '%s'", sLabel, sTargetLabel);   
          }
          
            break;
          
          case "nand" :
            break;
          
          default:
            return "Spécification de chaîne de caractères invalide";
        }  
      }
      break;
      
    case "str":
      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "length":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length != iLength) {
              return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
            }
  
            break;
            
          case "minLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length < iLength) {
              return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
            }
  
            break;
            
          case "maxLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length > iLength) {
              return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
            }
  
            break;

          default:
            return "Spécification de chaîne de caractères invalide";
        }
      };
      
      break;

    case "numchar":
    case "num":
      if (isNaN(oElement.value)) {
        return "N'est pas une chaîne numérique";
      }

      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "min":
            iMin = parseInt(aSpecFragments[2], 10);

            if (iMin == NaN) {
              return "Spécification de minimum numérique invalide";
            }
            
            if (oElement.value < iMin) {
              return printf("Soit avoir une valeur minimale de %s", iMin);
            }
            
            break;
          
          case "max":
              iMax = parseInt(aSpecFragments[2], 10);

              if (iMax == NaN) {
                return "Spécification de maximum numérique invalide";
              }
              
              if (oElement.value > iMax) {
                return printf("Soit avoir une valeur maximale de %s", iMin);
              }
              
              break;

          case "pos":
            if (oElement.value <= 0) {
              return "Doit avoir une valeur positive";
            }
            break;
          
          
          case "length":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length != iLength) {
              return printf("N'a pas la bonne longueur (longueur souhaité : %s)'", iLength);
            }
  
            break;
            
          case "minLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur minimale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length < iLength) {
              return printf("N'a pas la bonne longueur (longueur minimale souhaité : %s)'", iLength);
            }
  
            break;
            
          case "maxLength":
            iLength = parseInt(aSpecFragments[2], 10);
           
            if (iLength < 1 || iLength > 255) {
              return printf("Spécification de longueur maximale invalide (longueur = %s)", iLength);
            }

            if (oElement.value.length > iLength) {
              return printf("N'a pas la bonne longueur (longueur maximale souhaité : %s)'", iLength);
            }
  
            break;

          case "minMax":
            var iMin = parseInt(aSpecFragments[2], 10);
            var iMax = parseInt(aSpecFragments[3], 10);
            
            if (oElement.value > iMax || oElement.value < iMin) {
              return printf("N'est pas compris entre %i et %i", iMin, iMax);
            }
            
            break;
        }
      };
      
      break;
    
    case "bool":
      if (isNaN(oElement.value)) {
        return "N'est pas une chaîne numérique";
      }
      if(oElement.value!=0 && oElement.value!=1){
        return "Ne peut être différent de 0 ou 1";
      }
      break;
      
    case "enum":
      aSpecFragments.removeByIndex(0);
      if (!aSpecFragments.contains(oElement.value)) {
        return "N'est pas une valeur possible";
      }
      
      break;

    case "date":
      if(!oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)) {
        return "N'as pas un format correct";
      }
      
      break;

    case "time":
      if(!oElement.value.match(/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
        return "N'as pas un format correct";
      }
      
      break;

    case "dateTime":
      if(!oElement.value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})[ \+](\d{1,2}):(\d{1,2}):(\d{1,2})$/)) {
        return "N'as pas un format correct";
      }
      
      break;
      
    case "float":
    case "currency":
      //if (!oElement.value.match(/^(\d+)(\.\d{1,2})?$/)) {
      if(isNaN(parseFloat(oElement.value)) || parseFloat(oElement.value)!=oElement.value){
        return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
      }

      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "min":
            iMin = parseFloat(aSpecFragments[2], 10);
            if (iMin == NaN) {
              return "Spécification de minimum numérique invalide";
            }
            if (oElement.value < iMin) {
              return printf("Soit avoir une valeur minimale de %s", iMin);
            }
            break;
            
          case "max":
            iMax = parseFloat(aSpecFragments[2], 10);
            if (iMax == NaN) {
              return "Spécification de maximum numérique invalide";
            }
            if (oElement.value > iMax) {
              return printf("Soit avoir une valeur maximale de %s", iMin);
            }
            break;

          case "pos":
            if (oElement.value <= 0) {
              return "Doit avoir une valeur positive";
            }
            break;
          
          case "minMax":
            var iMin = parseFloat(aSpecFragments[2], 10);
            var iMax = parseFloat(aSpecFragments[3], 10);
            if (oElement.value > iMax || oElement.value < iMin) {
              return printf("N'est pas compris entre %i et %i", iMin, iMax);
            }
            break;
        };
      };  

      break;
    
    case "pct":
      if (!oElement.value.match(/^(\d+)(\.\d{1,2})?$/)) {
        return "N'est pas une valeur décimale (utilisez le . pour la virgule)";
      }
      
      break;
    
  case "text":
    break;
    
  case "html":
    break;
  case "email":
    if (!oElement.value.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
        return "Le format de l'email n'est pas valide";
      }
    break;
    case "code":
      if (sFragment1 = aSpecFragments[1]) {
        switch (sFragment1) {
          case "ccam":
            if (!oElement.value.match(/^([a-z]){4}([0-9]){3}$/i)) {
              return "Code CCAM incorrect, doit contenir 4 lettres et 3 chiffres";
            }
          
          break;

          case "cim10":
            if (!oElement.value.match(/^([a-z0-9]){0,5}$/i)) {
              return "Code CCAM incorrect, doit contenir 5 lettres maximum";
            }
            
            break;

          case "adeli":
            if (!oElement.value.match("/^([0-9]){9}$/i")) {
              return "Code Adeli incorrect, doit contenir exactement 9 chiffres";
            }
            
            break;

          case "insee":
            aMatches = oElement.value.match(/^([1-2][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i);
            if (!aMatches) {
              return "Matricule incorrect, doit contenir exactement 15 chiffres (commençant par 1 ou 2)";
            }

            nCode = parseInt(aMatches[1], 10);
            nCle = parseInt(aMatches[2], 10);
            if (97 - (nCode % 97) != nCle) {
              return "Matricule incorrect, la clé n'est pas valide";
            }
          
            break;

          default:
            return "Spécification de code invalide";
        }
      }

      break;
  }
  return null;
}

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
      aSpecFragments.removeByValue("confidential");
      var oLabel = getLabelFor(oElement);
      var aMsg = new Array;
      
      aSpecFragments.each(function (value) {
        if (sMsg = checkElement(oElement, value.split("|"))) {
          aMsg.push("\n => " + sMsg);
        }
      });
      
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