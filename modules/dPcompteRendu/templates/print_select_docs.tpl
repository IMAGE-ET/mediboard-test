{{if !$documents|@count}}
  <div class="small-info">
    {{tr}}CCompteRendu.none{{/tr}}
  </div>
{{else}}

  <script type="text/javascript">
    printDocuments = function() {
      var oIframe = Element.getTempIframe();
      var url = new Url("dPcompteRendu", "print_docs");
      url.addParam("dialog", 1);
      url.addParam("suppressHeaders", 1);
      url.addFormData("selectDocsFrm");
      url.redirect();
    }
  </script>
  <form name="selectDocsFrm" action="?" method="get">
    <input type="hidden" name="m" value="dPcompteRendu" />
    <input type="hidden" name="dialog" value="1" />
    <input type="hidden" name="a" value="print_docs" />
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
          <button type="button" class="print" onclick="printDocuments()">
            {{tr}}Print{{/tr}}
          </button>
        </td>
      </tr>
    </table>
  </form>
{{/if}}