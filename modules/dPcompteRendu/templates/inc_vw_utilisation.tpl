<table class="tbl">
  <tr>
    <th class="title">
      Liste des {{if $compte_rendu->type == "body"}}packs{{else}}modèles{{/if}} qui utilisent {{$compte_rendu->nom}}
    </th>
  </tr>
  {{foreach from=$modeles item=_modele}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_modele->_guid}}')">
          {{if $compte_rendu->type != "body"}}
          <a href="?m=compteRendu&tab=addedit_modeles&compte_rendu_id={{$_modele->_id}}">
          {{/if}}
            {{mb_value object=$_modele field=nom}}
          {{if $compte_rendu->type != "body"}}
          </a>
          {{/if}}
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
