<table class="main">
  <tr>
    <th class="title" colspan="2">
      '{{$compte_rendu->nom}}'
    </th>
  </tr>

  <td style="width: 50%;">
    <div style="max-height: 550px; overflow-y: auto;">

    <table class="tbl">
      <tr>
        <th>
          Liste des {{if $compte_rendu->type == "body"}} packs {{else}} modèles {{/if}}
        </th>
      </tr>

      {{foreach from=$modeles item=_modele}}
        <tr>
          <td class="text">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_modele->_guid}}')">
              {{if $compte_rendu->type != "body"}}
                <a href="?m=compteRendu&tab=addedit_modeles&compte_rendu_id={{$_modele->_id}}">
                  {{mb_value object=$_modele field=nom}}
                </a>
              {{else}}
                {{mb_value object=$_modele field=nom}}
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

    <div>
  </td>

  <td>
    <div style="max-height: 550px; overflow-y: auto;">

    <table class="tbl">
      <tr>
        <th colspan="2">
          Utilisateurs du modèle
        </th>
      </tr>

      {{foreach from=$counts key=_user_id item=_count}}
        {{assign var=user value=$users.$_user_id}}
        <tr>
          <td class="text">
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$user}}
          </td>
          <td class="narrow" style="text-align: center;">
            {{$_count}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="2" class="empty">
            {{tr}}CMediusers.none{{/tr}}
          </td>
        </tr>
      {{/foreach}}
    </table>

    </div>
  </td>

</table>
