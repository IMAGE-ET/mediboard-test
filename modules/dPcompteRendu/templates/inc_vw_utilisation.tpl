<table class="tbl">
  <tr>
    <th class="title">
      Liste des modèles qui utilisent {{$compte_rendu->nom}}
    </th>
  </tr>
  {{foreach from=$modeles item=_modele}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_modele->_guid}}')">
          <a href="?m=compteRendu&tab=addedit_modeles&compte_rendu_id={{$_modele->_id}}">
            {{mb_value object=$_modele field=nom}}
          </a>
        </span>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CCompteRendu.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>
