{{assign var=astreinte value=$object}}
{{mb_script module="astreintes" script="plage"}}
<table class="tbl tooltip">
  <tr>
    <td>
      {{mb_label object=$astreinte field=libelle}} :
      {{mb_value object=$astreinte field=libelle}}
    </td>
  </tr>
    {{if $astreinte->date_debut == $astreinte->date_fin}}
    <tr>
      <td>
        Le : {{mb_value object=$astreinte field=date_debut}}
      </td>
    </tr>
    {{else}}
    <tr>
      <td>
        {{mb_label object=$astreinte field=date_debut}} :
        {{mb_value object=$astreinte field=date_debut}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$astreinte field=date_fin}} :
        {{mb_value object=$astreinte field=date_fin}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_value object=$astreinte field=_duree}} {{tr}}Days{{/tr}}
      </td>
    </tr>
    {{/if}}
     <tr>
      <td>
        <button class="edit" onclick="PlageAstreinte.modal({{$astreinte->_id}}, {{$astreinte->user_id}})">{{tr}}Edit{{/tr}}</button>
      </td>
    </tr>
</table>

