<!--  $Id: vw_idx_listes.tpl 12241 2011-05-20 10:29:53Z flaviencrochard $ -->

{{if !$liste->_id}}
  <div class="small-info">Il faut créer la liste pour pouvoir en gérer les choix</div>
  {{mb_return}}
{{/if}}

<div style="height: 150px; overflow-y: auto;">

  <table class="tbl">
    <tr><th class="category" colspan="2">Choix disponibles</th></tr>
    {{foreach from=$liste->_valeurs item=_valeur name=choix}}
    <tr>
      <td class="text">{{$_valeur|nl2br}}</td>
      <td class="narrow">
        <form name="Add-Choix-{{$smarty.foreach.choix.iteration}}" action="?m={{$m}}" method="post" onsubmit="return ListeChoix.onSubmitChoix(this);">
  
        <input type="hidden" name="m"      value="{{$m}}" />
        <input type="hidden" name="dosql" value="do_liste_aed" />
        {{mb_key object=$liste}}
  
        {{mb_field object=$liste field=valeurs hidden=1}}
        <input type="hidden" name="_del" value="{{$_valeur}}" />
        <button class="remove notext compact" type="submit">{{tr}}Delete{{/tr}}</button>
        </form>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CListeChoix{{/tr}}</td>
    </tr>
    {{/foreach}}
  </table>

</div>
 
<form name="Add-Choix" action="?m={{$m}}" method="post" onsubmit="return ListeChoix.onSubmitChoix(this);">

  <input type="hidden" name="m"      value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_liste_aed" />
  {{mb_key object=$liste}}
  
  {{mb_field object=$liste field=valeurs hidden=1}}
  
  <table class="form">
    <tr>
      <th class="category" colspan="2">Ajouter un choix</th>
    </tr>
    <tr>
      <td>
        <textarea name="_new"></textarea>
      </td>
    </tr>
    <tr>
      <td class="button">
        <button type="submit" class="add">{{tr}}Add{{/tr}}</button>
      </td>
     </tr>
  </table>

</form>
