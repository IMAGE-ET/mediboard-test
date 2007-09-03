<!-- $Id: vw_resume.tpl 1748 2007-03-20 18:58:41Z MyttO $ -->

<script type="text/javascript">
  
var Intermax = {
  currentFunction : "unknown",
  newLine : {{$newLine|json}},
  
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
  
  ResultHandler : {
    "Lire Vitale" : function (oContent) {
      oVitale = oContent.VITALE;
      Console.debug(oVitale, "Trying to find patient");
      
      url = new Url;
      url.setModuleAction("dPpatients", "pat_selector");

      url.addParam("useVitale", "1");
      url.addParam("vitale[nom]", oVitale.VIT_NOM);
      url.addParam("vitale[prenom]", oVitale.VIT_PRENOM);

      var sAdresse = [
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1, 
        oVitale.VIT_ADRESSE_2,
        oVitale.VIT_ADRESSE_1].without("").join("\n");
      url.addParam("vitale[adresse]", sAdresse);
      
      var sNaissance = Date.fromLocaleDate(oVitale.VIT_DATE_NAISSANCE).toDATE();
      url.addParam("vitale[naissance]", sNaissance);
      
      var sMatricule = oVitale.VIT_NUMERO_SS_INDIV ?
        oVitale.VIT_NUMERO_SS_INDIV + oVitale.VIT_CLE_SS_INDIV :
        oVitale.VIT_NUMERO_SS + oVitale.VIT_CLE_SS
      url.addParam("vitale[matricule]", sMatricule);
      url.popup(800, 500, "Patient");
      
      window.setPat = function(patient_id, patient_view) {
        Console.debug(patient_id, "Patient ID");
        Console.debug(patient_view, "Patient view");
      }
    }
  }
}
</script>

<table class="tbl">
  <!-- Yoplets for InterMax -->
  <tr class="intermax-yoplet">
    <th>Déclenchement de fonctions</th>
    <th>Lecture de résultat</th>
  </tr>
  
  <tr>
    <td style="text-align: center">
    <!-- Yoplet to trigger functions -->

    <applet 
      name="intermaxTrigger"
      code="org.yoplet.Yoplet.class" 
      archive="includes/applets/yoplet.jar" 
      width="400" 
      height="200"
    >
      <param name="action" value="sleep"/>
      <param name="lineSeparator" value="{{$newLine}}"/>
      <param name="debug" value="true" />
      <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.INI" />
      <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/CALL.FLG" />
    </applet>

    </td>

    <td style="text-align: center">

    <!-- Yoplet to read results -->
    <applet 
      name="intermaxResult"
      code="org.yoplet.Yoplet.class" 
      archive="includes/applets/yoplet.jar" 
      width="400" 
      height="200"
    >
      <param name="action" value="sleep"/>
      <param name="lineSeparator" value="{{$newLine}}"/>
      <param name="debug" value="true" />
      <param name="filePath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/INTERMAX.OUT" />
      <param name="flagPath" value="{{$app->user_prefs.InterMaxDir}}/INTERMAX/RETURN.FLG" />
    </applet>

    </td>
  </tr>
  
  <tr>
    <th>Fonctions disponibles</th>
    <th>
      <button class="tick result" onclick="Intermax.result();" style="float:right">
        {{tr}}InterMax.Result{{/tr}}
      </button>
      Résultats
    </th>
  </tr>

  {{foreach from=$intermaxFunctions item="_function"}}
  <tr id="{{$_function|replace:" ":"-"}}">
    <td>
      <button class="tick" onclick="Intermax.trigger('{{$_function}}');">
        {{tr}}InterMax.{{$_function}}{{/tr}}
      </button>
    </td>
    <td class="result">
      <div class="handler">My Result</div>
    </td>
  </tr>
  {{/foreach}}
  
</table>