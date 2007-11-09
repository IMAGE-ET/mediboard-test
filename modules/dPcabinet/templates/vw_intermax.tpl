<!-- $Id: vw_resume.tpl 1748 2007-03-20 18:58:41Z MyttO $ -->

{{mb_include_script module="dpPatients" script="pat_selector"}}
{{include file="../../dPpatients/templates/inc_intermax.tpl" debug="false"}}

<script type="text/javascript">

Intermax.createResultMessages = function() {
  // Select div result handler      
  var idFonction = this.oContent.FONCTION.NOM.replace(/ /g, "-");
  var eResultHandler = $(idFonction);
  eResultHandler.innerHTML = "";

  // Create handler messages
  oParam = this.oContent.PARAM;
  eResultHandler.appendChild(Dom.createMessage("Appel : " + oParam.APPEL, oParam.APPEL == "OK" ? "message" : "error"))
  eResultHandler.appendChild(Dom.createMessage("Exécution: " + oParam.EXECUTION, oParam.EXECUTION == "OK" ? "message" : "error"))
  if (oParam.ERREUR) eResultHandler.appendChild(Dom.createMessage("Erreur : " + oParam.ERREUR, oParam.ERREUR == undefined  ? "message" : "error"))
  if (oParam.ERREUR_API) eResultHandler.appendChild(Dom.createMessage("Erreur API : " + oParam.ERREUR_API, oParam.ERREUR_API == undefined ? "message" : "error"))
}

Intermax.ResultHandler["Consulter Vitale"] =
Intermax.ResultHandler["Lire Vitale"] = function() {
  PatSelector.set = function(id, view) {
    Console.debug(view, "Found and update patient");
    Console.debug(id, "With ID");
  }
  
  PatSelector.options.useVitale = 1;
  PatSelector.pop();
}

</script>

<table class="tbl">
  <!-- Yoplets for InterMax -->
  <tr class="intermax-yoplet">
    <th>Déclenchement de fonctions</th>
    <th>Lecture de résultat</th>
  </tr>
  
  <tr>
    <th class="title">Fonctions disponibles</th>
    <th class="title">
      <button class="tick result" onclick="Intermax.result();" style="float:right">
        {{tr}}InterMax.Result{{/tr}}
      </button>
      Résultats
    </th>
  </tr>

  {{foreach from=$intermaxFunctions key=category item=_functions}}
  <tr>
    <th colspan="10">{{$category}}</th>
  </tr>
  {{foreach from=$_functions item=_function}}
  <tr>
    <td>
      <button class="tick" onclick="Intermax.trigger('{{$_function}}');">
        {{$_function}}
      </button>
    </td>
    <td id="{{$_function|replace:" ":"-"}}">
      <div class="handler">My Result</div>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  
</table>