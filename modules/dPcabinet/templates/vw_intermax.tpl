<!-- $Id: vw_resume.tpl 1748 2007-03-20 18:58:41Z MyttO $ -->

{{mb_include_script path="includes/javascript/intermax.js"}}

<script type="text/javascript">

Intermax.initialize = function(){
  Intermax.url.setModuleAction("dPpatients", "pat_selector"); 
}

Intermax.action = function(){
  Intermax.url.popup(800, 500, "Patient");
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