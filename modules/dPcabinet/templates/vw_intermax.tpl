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
      if (aMatches = line.match(/(\w*)=(\w*)/)) {
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
    Console.debug(this.currentFunction, "Waiting for result of InterMax function");
    document.intermaxResult.performRead();
    if (oAppletContent = document.intermaxResult.getContent()) {
      // Append with empty Js String will cast a Java string to a Js string
      var sContent = oAppletContent + ""; 
      var oContent = this.bindContent(sContent);
      Console.debug(oContent, "Result is", { level: 2} );
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
    <td>
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
    

    <td>

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
    <th>Résultats</th>
  </tr>
  <tr id="Lire-Vitale">
    <td>
      <button class="tick" onclick="Intermax.trigger('Lire Vitale');">
        {{tr}}InterMax.Lire Vitale{{/tr}}
      </button>
    </td>
    <td>
      <button class="tick result" onclick="Intermax.result();">
        {{tr}}InterMax.Result{{/tr}}
      </button>
    </td>
  </tr>
</table>