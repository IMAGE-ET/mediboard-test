{{if !$documents|@count}}
  <strong>Il n'y a pas de document pour cette consultation</strong>
{{else}}
  <script type="text/javascript">
  function nbDocMoins(doc_id){
    var oForm = document.selectDocsFrm;
    var oElement = oForm.elements["nbDoc["+doc_id+"]"];
    if(parseInt(oElement.value) > 0){
      oElement.value = parseInt(oElement.value) - 1;
    }
  }
  function nbDocPlus(doc_id){
    var oForm = document.selectDocsFrm;
    var oElement = oForm.elements["nbDoc["+doc_id+"]"];
    oElement.value = parseInt(oElement.value) + 1;
  }
  </script>
  
  <form name="selectDocsFrm" action="?m={{$m}}&amp;dialog=1&amp;a=print_docs" method="post">
  <input type="hidden" name="consultation_id" value="{{$consult->consultation_id}}" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        Veuillez choisir le nombre de documents à imprimer
      </th>
    </tr>
    {{foreach from=$documents item=curr_doc}}
    <tr>
      <th class="text">
        {{$curr_doc->nom}}
      </th>
      <td class="button">
        <button type="button" onclick="nbDocMoins({{$curr_doc->compte_rendu_id}});" class="remove notext"></button>
        <input name="nbDoc[{{$curr_doc->compte_rendu_id}}]" type="text" size="2" value="1" readonly="readonly" />
        <button type="button" onclick="nbDocPlus({{$curr_doc->compte_rendu_id}});" class="add notext"></button>
      </td>
    </tr>
    {{/foreach}}
    <tr>
      <td class="button" colspan="2">
        <button type="submit" class="print">
          {{tr}}Print{{/tr}}
        </button>
      </td>
    </tr>
  </table>
  </form>
{{/if}}