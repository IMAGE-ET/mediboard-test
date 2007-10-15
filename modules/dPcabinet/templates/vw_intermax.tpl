<!-- $Id: vw_resume.tpl 1748 2007-03-20 18:58:41Z MyttO $ -->

{{mb_include_script path="includes/javascript/intermax.js"}}
{{mb_include_script module="dpPatients" script="pat_selector"}}

<script type="text/javascript">

Intermax.createResultMessages = function(oContent) {
  // Select div result handler      
  var idFonction = oContent.FONCTION.NOM.replace(/ /g, "-");
  var eResultHandler = $(idFonction);
  eResultHandler.innerHTML = "";

  // Create handler messages
  oParam = oContent.PARAM;
  eResultHandler.appendChild(Dom.createMessage("Appel : " + oParam.APPEL, oParam.APPEL == "OK" ? "message" : "error"))
  eResultHandler.appendChild(Dom.createMessage("Exécution: " + oParam.EXECUTION, oParam.EXECUTION == "OK" ? "message" : "error"))
  eResultHandler.appendChild(Dom.createMessage("Erreur : " + oParam.ERREUR, oParam.ERREUR == undefined  ? "message" : "error"))
  eResultHandler.appendChild(Dom.createMessage("Erreur API : " + oParam.ERREUR_API, oParam.ERREUR_API == undefined ? "message" : "error"))
}

Intermax.ResultHandler = {
  "Lire Vitale": function() {
    PatSelector.set = function(id, view) {
      Console.debug(view, "Found and update patient");
      Console.debug(id, "With ID");
    }
    
    PatSelector.options.useVitale = 1;
    PatSelector.pop();
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
    <td style="text-align: center" colspan="2">
    {{assign var="debug" value="true"}}
    {{include file="../../dPpatients/templates/inc_intermax.tpl"}}
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
  <tr>
    <td>
      <button class="tick" onclick="Intermax.trigger('{{$_function}}');">
        {{tr}}InterMax.{{$_function}}{{/tr}}
      </button>
    </td>
    <td id="{{$_function|replace:" ":"-"}}">
      <div class="handler">My Result</div>
    </td>
  </tr>
  {{/foreach}}
  
</table>