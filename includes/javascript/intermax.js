var Intermax = {
  currentFunction : "unknown",
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
    document.intermaxResult.performRead();
    setTimeout(Intermax.handleContent.bind(Intermax), 100);
    
  },
  
  handleContent: function() {
    if (oAppletContent = document.intermaxResult.getContent()) {
      // Append with empty Js String will cast a Java string to a Js string
      var sContent = oAppletContent + ""; 
      oContent = this.bindContent(sContent);
      this.createResultMessages(oContent);
      
      this.sendContent(oContent);
    }
  },
  
  sendContent: function(oContent) {
    var url = new Url;
    url.setModuleDosql("dPpatients", "httpreq_intermax_content");
    url.addObjectParam("intermax", oContent);
    url.requestUpdate(SystemMessage.id, { method: "post" });
  },
  
  createResultMessages: Prototype.emptyFunction,
   
  handleResult: function(sFunction) {
    
		// Activate function handler
    var fResultHandler = this.ResultHandler[oContent.FONCTION.NOM] || function() { 
      Console.debug(sFunction, "Unhandled InterMax function"); 
    }
    fResultHandler(oContent);
  },

  ResultHandler : {
  }
}