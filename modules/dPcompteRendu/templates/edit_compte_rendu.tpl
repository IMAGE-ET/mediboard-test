{literal}
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

</script>
{/literal}

<form name="editFrm" action="?m={$m}" method="post" onsubmit="return submitCR();">

<input type="hidden" name="m" value="dPcompteRendu" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="dosql" value="do_modele_aed" />
<input type="hidden" name="compte_rendu_id" value="{$compte_rendu->compte_rendu_id}" />
<input type="hidden" name="object_id" value="{$compte_rendu->object_id}" />
<input type="hidden" name="type" value="{$compte_rendu->type}" />

<table class="form">
  <tr>
    <th class="category">
      <strong>Nom du document :</strong>
      <input name="nom" size="50" value="{$compte_rendu->nom}">
    </th>
  <tr>
    <td class="listeChoixCR" id="liste">
      {if $lists|@count}
      <ul>
        {foreach from=$lists item=curr_list}
        <li>
          <select name="_liste{$curr_list->liste_choix_id}">
            <option value="undef">&mdash; {$curr_list->nom} &mdash;</option>
            {foreach from=$curr_list->_valeurs item=curr_valeur}
            <option>{$curr_valeur}</option>
            {/foreach}
          </select>
        </li>
        {/foreach}
        <li>
          <button type="submit"><img src="modules/{$m}/images/tick.png" /></button>
        </li>
      </ul>
      {/if}
    </td>
  </tr>
  <tr>
    <td style="height: 600px">
      <textarea id="htmlarea" name="source">
        {$templateManager->document}
      </textarea>
    </td>
  </tr>

</table>

</form>