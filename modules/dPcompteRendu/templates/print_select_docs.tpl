{{if !$documents|@count}}
  <div class="small-info">
    {{tr}}CCompteRendu.none{{/tr}}
  </div>
{{else}}
  <form name="selectDocsFrm" action="?" method="get" target="_blank">
    <input type="hidden" name="m" value="dPcompteRendu" />
    <input type="hidden" name="dialog" value="1" />
    <input type="hidden" name="a" value="print_docs" />
    <input type="hidden" name="suppressHeaders" value="1" />
    <table class="main form">
      <tr>
        <th class="category" colspan="2">
          Veuillez choisir le nombre de documents à imprimer
        </th>
      </tr>
      {{foreach from=$documents item=curr_doc}}
      <tr>
        <th>
          {{$curr_doc->nom}}
        </th>
        <td>
          <input name="nbDoc[{{$curr_doc->compte_rendu_id}}]" type="text" size="2" value="1" />
          <script type="text/javascript">
            $(getForm("selectDocsFrm").elements['nbDoc[{{$curr_doc->compte_rendu_id}}]']).addSpinner({min:0});
          </script>
        </td>
      </tr>
      {{/foreach}}
      <tr>
        <td class="button" colspan="2">
          <button class="pdf">{{tr}}Print{{/tr}}</button>
        </td>
      </tr>
    </table>
  </form>
{{/if}}