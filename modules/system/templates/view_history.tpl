<script type="text/javascript">

function pageMain() {
  regFieldCalendar("filterFrm", "_date_min", true);
  regFieldCalendar("filterFrm", "_date_max", true);
}

</script>

{{if !$dialog}}
<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="dialog" value="{{$dialog}}" />

<table class="form">
  <tr>
    <th class="category" colspan="10">
      {{if $list|@count == 100}}
      Plus de 100 historiques, seuls les 100 plus récents sont affichés
      {{else}}
      {{$list|@count}} historiques trouvés
      {{/if}}
    </th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$filter field=user_id}}</th>
    <td>
      <select name="user_id" class="ref">
        <option value="">&mdash; Tous les utilisateurs</option>
        {{foreach from=$listUsers item=curr_user}}
        <option value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $filter->user_id}}selected="selected"{{/if}}>
          {{$curr_user->_view}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=object_class}}</th>
    <td>
      <select name="object_class" class="str maxLength|25">
        <option value="0">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{tr}}{{$curr_class}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field="_date_min"}}</td>
    <td class="date">{{mb_field object=$filter field="_date_min" form="filterFrm" canNull=false}} </td>

  </tr>
  <tr>
    <th>{{mb_label object=$filter field=type}}</th>
    <td>
      <select name="type" class="enum list|0|create|store|delete">
        <option value="0">&mdash; Tous les types</option>
        {{html_options options=$userLog->_enumsTrans.type selected=$filter->type}}
      </select>
    </td>

    <th>{{mb_label object=$filter field=object_id}}</th>
    <td>{{mb_field object=$filter field=object_id canNull=true}}</td>

    <th>{{mb_label object=$filter field="_date_max"}}</td>
    <td class="date">{{mb_field object=$filter field="_date_max" form="filterFrm" canNull=false}} </td>
  </tr>

  <tr>
    <td class="button" colspan="10">
      <button class="search">{{tr}}Search{{/tr}}</button>
    </td>
  </tr>
</table>
</form>
{{/if}}
<table class="tbl">
  {{if $dialog}}
  <tr>
    <th colspan="4" class="title">
      {{if $list|@count > 0}}
      Historique de {{$item}}
      {{else}}
      Pas d'historique
      {{/if}}
    </th>
  </tr>
  {{/if}}
  <tr>
    <th>Utilisateur</th>
    {{if !$dialog}}
    <th>classe</th>
    <th>Objet</th>
    {{/if}}
    <th>Date</th>
    <th>Action</th>
    <th>Champs</th>
  </tr>
  {{foreach from=$list item=curr_object}}
  <tr>
    <td>{{$curr_object->_ref_user->_view}} ({{$curr_object->user_id}})</td>
    {{if !$dialog}}
    <td>{{$curr_object->object_class}}</td>
    <td>{{$curr_object->_ref_object->_view}} ({{$curr_object->object_id}})</td>
    {{/if}}
    <td>{{$curr_object->date|date_format:"%d/%m/%Y à %Hh%M (%A)"}}</td>
    <td>{{tr}}CUserLog.type.{{$curr_object->type}}{{/tr}}</td>
    <td>
      {{foreach from=$curr_object->_fields|smarty:nodefaults item=curr_field}}
      {{$curr_field}}<br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>