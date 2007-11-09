var Intermax = {
  oContent : {},
  currentFunction : "unknown",
  newLine : "---",
  
  alert: function(sError) {
    var sLogicMaxFonction = this.oContent.FONCTION ? "LogicMax : " +  this.oContent.FONCTION.NOM : "Aucun appel LogicMax";
    var sLogicMaxError = sError.charAt(0) == "-" ? "\n\n[Erreur LogicMax "+sError+"]" : "";
    alert(sLogicMaxFonction + "\n\n" + Intermax.errors[sError] + sLogicMaxError);
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
    this.currentFunction = sFunction;
    Console.debug(this.currentFunction, "Trigger InterMax function");
    
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
  
  result: function() {
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
    
	  if (this.oContent.PARAM.EXECUTION == 'KO') {
	    Intermax.alert(this.oContent.PARAM.ERREUR);
	    return;
	  }
    
    // 
    this.createResultMessages();
    this.sendContent();
  },
  
  sendContent: function() {
    var url = new Url;
    url.setModuleDosql("dPpatients", "httpreq_intermax_content");
    url.addObjectParam("intermax", this.oContent);
    url.requestUpdate(SystemMessage.id, { method: "post" } );
  },
  
  createResultMessages: Prototype.emptyFunction,
   
  handleResult: function(sFunction) {
    
		// Activate function handler
    var fResultHandler = this.ResultHandler[this.oContent.FONCTION.NOM] || function() { 
      Console.debug(sFunction, "Unhandled InterMax function"); 
    }
    fResultHandler();
  },

  ResultHandler : {
  }
}