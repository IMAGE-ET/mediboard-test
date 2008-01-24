<script type="text/javascript">

function submitCR() {
  return true;
}

function refreshCR() {
  oForm = document.editFrm;
  var listUrl = new Url;
  listUrl.setModuleAction("dPcompteRendu", "httpreq_liste_choix_cr");
  listUrl.addParam("compte_rendu_id", oForm.compte_rendu_id.value);
  listUrl.requestUpdate('liste');

  var sourceUrl = new Url;
  sourceUrl.setModuleAction("dPcompteRendu", "httpreq_source_cr");
  sourceUrl.addParam("compte_rendu_id", oForm.compte_rendu_id.value);
  sourceUrl.requestUpdate('htmlarea');
}

if (window.opener.reloadAfterSaveDoc) {
  window.opener.reloadAfterSaveDoc({{$compte_rendu->object_id}});
}

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">

<input type="hidden" name="m" value="dPcompteRendu" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="function_id" value="" />
<input type="hidden" name="chir_id" value="" />
{{mb_field object=$compte_rendu field="compte_rendu_id" hidden=1 prop=""}}
{{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
{{mb_field object=$compte_rendu field="object_class" hidden=1 prop=""}}

<table class="form">
  <tr>
    <th class="category">
      <strong>Nom du document :</strong>
      <input name="nom" size="50" value="{{$compte_rendu->nom}}" />
      &mdash;
      <strong>Catégorie :</strong>
      <select name="file_category_id">
      <option value=""{{if !$compte_rendu->file_category_id}} selected="selected"{{/if}}>&mdash; Aucune Catégorie</option>
      {{foreach from=$listCategory item=currCat}}
      <option value="{{$currCat->file_category_id}}"{{if $currCat->file_category_id==$compte_rendu->file_category_id}} selected="selected"{{/if}}>{{$currCat->nom}}</option>
      {{/foreach}}
      </select>
    </th>
  <tr>
    <td class="listeChoixCR" id="liste">
      {{if $lists|@count}}
      <ul>
        {{foreach from=$lists item=curr_list}}
        <li>
          <select name="_liste{{$curr_list->liste_choix_id}}">
            <option value="undef">&mdash; {{$curr_list->nom}} &mdash;</option>
            {{foreach from=$curr_list->_valeurs|smarty:nodefaults item=curr_valeur}}
            <option>{{$curr_valeur}}</option>
            {{/foreach}}
          </select>
        </li>
        {{/foreach}}
        <li>
          <button class="tick notext" type="submit">{{tr}}Save{{/tr}}</button>
        </li>
      </ul>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td style="height: 600px">
      <textarea id="htmlarea" name="source">
        {{$templateManager->document}}
      </textarea>
    </td>
  </tr>

</table>

</form>