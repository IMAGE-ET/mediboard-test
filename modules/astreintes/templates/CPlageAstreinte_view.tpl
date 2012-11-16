{{assign var=astreinte value=$object}}
{{mb_script module="astreintes" script="plage"}}
<table class="tbl tooltip">
  <tr>
    <th>
      {{tr}}CPlageAstreinte{{/tr}} {{if $astreinte->libelle}}"{{mb_value object=$astreinte field=libelle}}"{{/if}}
    </th>
  </tr>
    <tr>
      <td>{{mb_value object=$astreinte field=user_id}}</td>
    </tr>

    <tr>
      <td>
        {{if $astreinte->date_debut == $astreinte->date_fin}}
          Le : {{mb_value object=$astreinte field=date_debut}}
        {{else}}
          {{$astreinte->date_debut|date_format:$conf.longdate}} &rarr; {{$astreinte->date_fin|date_format:$conf.longdate}}<br/>
          {{if $astreinte->_duree}}{{$astreinte->_duree}} {{tr}}Days{{/tr}}{{/if}}
        {{/if}}
      </td>
    </tr>

    {{if $astreinte->_ref_user}}
    <tr>
      <td>
        {{if $astreinte->_num_astreinte}}
          <a class="button phone">{{mb_value object=$astreinte field=_num_astreinte}}</a>
        {{else}}
          <a class="button warning">{{tr}}CPlageAstreinte.noPhoneNumber{{/tr}}</a>
        {{/if}}
      </td>
    </tr>
    {{/if}}
     <tr style="text-align: center;">
      <td>
        <button class="edit" onclick="PlageAstreinte.modal({{$astreinte->_id}}, {{$astreinte->user_id}})">{{tr}}Edit{{/tr}}</button>
      </td>
      </td>
    </tr>
</table>

