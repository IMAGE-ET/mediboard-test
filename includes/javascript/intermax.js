var Intermax = {
  currentFunction : "unknown",
  //newLine : {{$newLine|json}},
  newLine : "---",
  
  bindContent: function(sContent) {
    var aContentLines = sContent.split(this.newLine);
    var oContent = {}
    var sCurrentCategory = "";
    aContentLines.each(function(line) {
      
      // Create new category
      if (aMatches = line.match(/\[(\w*)\]/)) {
        sCurrentCategory = aMatches[1];
        oContent[sCurrentCategory] = {}
      }
      
      // Fill a key-value pair in current category
      if (aMatches = line.match(/(\w*)=(.*)/)) {
        sKey = aMatches[1];
        sValue = aMatches[2];
        oContent[sCurrentCategory][sKey] = sValue;
      }
      
    } );
    
	return oContent;
  },
  
  makeContent: function(oContent) {
    var sContent = '';
    $H(oContent).each(function(pair) {
      sContent += printf ("[%s]%s", pair.key, Intermax.newLine);
      $H(pair.value).each( function(pair) {
        sContent += printf ("%s = %s%s", pair.key, pair.value, Intermax.newLine);
      } );
    } );  
    return sContent;
  },

  trigger: function(sFunction) {
    this.currentFunction = sFunction;
    Console.debug(this.currentFunction, "Trigger InterMax function");
    
    var oContent = {
      FONCTION: {
        NOM: sFunction
      },
      PARAM: {
        AFFICHAGE: 1
      }
    }
    
    var sContent = this.makeContent(oContent);
    document.intermaxTrigger.performWrite(sContent);
  },
  
  result: function() {
    Console.trace("Waiting for result of InterMax");
    document.intermaxResult.performRead();
    setTimeout(Intermax.handleContent.bind(Intermax), 100);
    
  },
  
  handleContent: function() {
    if (oAppletContent = document.intermaxResult.getContent()) {
      // Append with empty Js String will cast a Java string to a Js string
      var sContent = oAppletContent + ""; 
      oContent = this.bindContent(sContent);
      Console.debug(oContent, "Result is", { level: 1 } );
      this.createResultMessages(oContent);
      var fResultHandler = this.ResultHandler[oContent.FONCTION.NOM] || Prototype.emptyFunction;
      fResultHandler(oContent);
    }
  },
  
  createResultMessages: function(oContent) {
    var idFonction = oContent.FONCTION.NOM.replace(" ", "-");

    // Select div result handler      
    var sSelector = "tr#" + idFonction + " td.result div.handler";
    var eResultHandler = $$(sSelector)[0];
    eResultHandler.innerHTML = "";

    // Create handler messages
    oParam = oContent.PARAM;
    eResultHandler.appendChild(Dom.createMessage("Appel : " + oParam.APPEL, oParam.APPEL == "OK" ? "message" : "error"))
    eResultHandler.appendChild(Dom.createMessage("Exécution: " + oParam.EXECUTION, oParam.EXECUTION == "OK" ? "message" : "error"))
    eResultHandler.appendChild(Dom.createMessage("Erreur : " + oParam.ERREUR, oParam.ERREUR == undefined  ? "message" : "error"))
    eResultHandler.appendChild(Dom.createMessage("Erreur API : " + oParam.ERREUR_API, oParam.ERREUR_API == undefined ? "message" : "error"))
  },
  
 
  url : new Url(),
  
  initialize: function() {
  },
  
  action: function(){
  },
 
  ResultHandler : {
    "Lire Vitale" : function (oContent) {
 
      oVitale = oContent.VITALE;
      Console.debug(oVitale, "Trying to find patient");
      
      Intermax.initialize();
      
      Intermax.url.addParam("useVitale"              , "1"                      );
      Intermax.url.addParam("vitale[nom]"            , oVitale.VIT_NOM          );
      Intermax.url.addParam("vitale[prenom]"         , oVitale.VIT_PRENOM       );
     
      Intermax.url.addParam("vitale[assure_nom]"     , oVitale.VIT_NOM_ASSURE   );
      Intermax.url.addParam("vitale[assure_prenom]"  , oVitale.VIT_PRENOM_ASSURE);
      
      /*
      Intermax.url.addParam("vitale[]", oVitale.VIT_CODE_REGIME  );
      Intermax.url.addParam("vitale[]", oVitale.VIT_CAISSE_GEST  );
      Intermax.url.addParam("vitale[]", oVitale.VIT_CENTRE_GEST  );
      Intermax.url.addParam("vitale[]", oVitale.VIT_NOM_AMO      );
      Intermax.url.addParam("vitale[]", oVitale.AMO_NB_PERIODE   );
      Intermax.url.addParam("vitale[]", oVitale.MUT_NUMERO       );
      Intermax.url.addParam("vitale[]", oVitale.MUT_DROITS       );
      */
      
      var sAdresse = [
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1].without("").join("\n");
      Intermax.url.addParam("vitale[adresse]", sAdresse);
      
      var sNaissance = Date.fromLocaleDate(oVitale.VIT_DATE_NAISSANCE).toDATE();
      Intermax.url.addParam("vitale[naissance]", sNaissance);
      
      var sMatricule = oVitale.VIT_NUMERO_SS_INDIV ?
        oVitale.VIT_NUMERO_SS_INDIV + oVitale.VIT_CLE_SS_INDIV :
        oVitale.VIT_NUMERO_SS + oVitale.VIT_CLE_SS
      Intermax.url.addParam("vitale[matricule]", sMatricule);
    
      Intermax.action();
      
      window.setPat = function(patient_id, patient_view) {
        Console.debug(patient_id, "Patient ID");
        Console.debug(patient_view, "Patient view");
      }
    }
  }
}