/**
 * Class for LogicMax browser based Integration
 */
 
var Intermax = {
  oContent: {},
  aAllowedFunctions: [],
  newLine: "---",
  
  alert: function(sError, sExtraInfo) {
    sExtraInfo = sExtraInfo || "";
    
    // Error message preparing
    var sLogicMaxFonction = this.oContent.FONCTION ? "Fonction LogicMax : " +  this.oContent.FONCTION.NOM : "Aucun appel à LogicMax";
    var sLogicMaxError = sError != "0" ? "\n\n[Code LogicMax "+sError+"]" : "";

    // Actual alert
    sErrorMessage = Intermax.errors[sError] || "Description non traduite";
    alert(sLogicMaxFonction + "\n\n" + sErrorMessage + sExtraInfo + sLogicMaxError);
  },
  
  bindContent: function(sContent) {    
    var aContentLines = sContent.split(this.newLine);
    this.oContent = {}
    var sCurrentCategory = "";
    aContentLines.each(function(line) {
      
      // Create new category
      if (aMatches = line.match(/\[(\w*)\]/)) {
        sCurrentCategory = aMatches[1];
        Intermax.oContent[sCurrentCategory] = {}
      }
      
      // Fill a key-value pair in current category
      if (aMatches = line.match(/(\w*)=(.*)/)) {
        sKey = aMatches[1];
        sValue = aMatches[2];
        Intermax.oContent[sCurrentCategory][sKey] = sValue;
      }
      
    } );
  },
    
  makeContent: function() {
    var sContent = '';
    $H(this.oContent).each(function(pair) {
      sContent += printf ("[%s]%s", pair.key, Intermax.newLine);
      $H(pair.value).each( function(pair) {
        sContent += printf ("%s = %s%s", pair.key, pair.value, Intermax.newLine);
      } );
    } );  
    return sContent;
  },

  trigger: function(sFunction, oCallContent) {
    
    this.oContent = {
      FONCTION: {
        NOM: sFunction
      },
      PARAM: {
        AFFICHAGE: 1
      }
    }
    Object.extend(this.oContent, oCallContent);
        
    var sContent = this.makeContent();
    document.intermaxTrigger.performWrite(sContent);
  },
  
  result: function(aAllowedFunctions) {
    this.aAllowedFunctions = aAllowedFunctions ? [aAllowedFunctions].flatten() : [];
    document.intermaxResult.performRead();
    setTimeout(Intermax.handleContent.bind(Intermax), 100);
  },
  
  handleContent: function() {
    // Append with empty Js String will cast a Java string to a Js string
    var sContent = document.intermaxResult.getContent() + ""; 
    this.bindContent(sContent);
    if (!$H(this.oContent).values().length) {
	    Intermax.alert("100");
      return;
    }

		// Fonctions autorisées    
    if (this.aAllowedFunctions.length && !this.aAllowedFunctions.include(this.oContent.FONCTION.NOM)) {
	    Intermax.alert("110", this.aAllowedFunctions.join(", "));
	    return;
    }
    
    // Fonction en cours d'execution
    if (!this.oContent.PARAM.EXECUTION) {
	    Intermax.alert("120");
	    return;
    }
    
	  if (this.oContent.PARAM.EXECUTION == 'KO') {
	    Intermax.alert(this.oContent.PARAM.ERREUR);
	    return;
	  }
    
    this.sendContent();
  },
  
  sendContent: function() {
    var url = new Url;
    url.setModuleDosql("dPpatients", "httpreq_intermax_content");
    url.addObjectParam("intermax", this.oContent);
    url.requestUpdate(SystemMessage.id, { method: "post" } );
  },
   
  handleResult: function(sFunction) {
    
		// Activate function handler
    var fResultHandler = this.ResultHandler[this.oContent.FONCTION.NOM] || this.ResultHandler["all"];
    fResultHandler();
  },

  ResultHandler : {
    "all": function() {
	    Intermax.alert("200");
    }
  },
  
  Triggers : {
    "Consulter Vitale" : function(iVitale) {
			Intermax.trigger("Consulter Vitale", { 
				PARAM: { 
					AFFICHAGE: 1 
				}, 
				VIT: { 
					VIT_NUMERO_LOGICMAX: iVitale
				}
			} );
    },

    "Formater FSE" : function(iVitale, iCPS) {
      var oContent = { 
        CPS: {
          CPS_NUMERO_LOGICMAX: iVitale
        },
        VITALE: {
          VIT_NUMERO_LOGICMAX: iCPS
        }
      }
      
      // Won't extend properly with an array
      if (Intermax.Triggers['Formater FSE'].aActes.length == 0) {
      	Intermax.Triggers['Formater FSE'].aActes = {};
      }
      
      Object.extend(oContent, Intermax.Triggers['Formater FSE'].aActes);
			Intermax.trigger("Formater FSE", oContent);
    },

    "Consulter FSE" : function(iFSE) {
			Intermax.trigger("Consulter FSE", { 
        PARAM: {
          AFFICHAGE: 1
        },
        FSE: {
          FSE_NUMERO_FSE: iFSE
        } 
			} );
    },

    "Editer FSE" : function(iFSE) {
			Intermax.trigger("Editer FSE", { 
        PARAM: {
          AFFICHAGE: 1
        },
        FSE: {
          FSE_NUMERO_FSE: iFSE
        } 
			} );
    },

    "Annuler FSE" : function(iFSE) {
			Intermax.trigger("Annuler FSE", { 
        PARAM: {
          AFFICHAGE: 1
        },
        FSE: {
          FSE_NUMERO_FSE: iFSE
        } 
			} );
    }

  }
}