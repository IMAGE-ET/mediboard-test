{{mb_include_script module="system" script="object_selector"}}

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
      <select name="object_class" class="str">
        <option value="0">&mdash; Toutes les classes</option>
        {{foreach from=$listClasses item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $filter->object_class}}selected="selected"{{/if}}>
          {{tr}}{{$curr_class}}{{/tr}}
        </option>
        {{/foreach}}
      </select>
    </td>

    <th>{{mb_label object=$filter field="_date_min"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_min" form="filterFrm" register=true}}</td>

  </tr>
  <tr>
    <th>{{mb_label object=$filter field=type}}</th>
    <td>
			{{mb_field object=$filter field=type canNull=true defaultOption="&mdash; Choisir un type"}}
    </td>

    <th>{{mb_label object=$filter field=object_id}}</th>
    <td>{{mb_field object=$filter field=object_id canNull=true}}
    <button type="button" class="search" onclick="ObjectSelector.init()">
        Chercher un objet
      </button>
      <script language="Javascript" type="text/javascript">
        ObjectSelector.init = function(){  
          this.sForm     = "filterFrm";
          this.sId       = "object_id";
          this.sView     = "object_id";
          this.sClass    = "object_class";
          this.onlyclass = "false";
          this.pop();
        } 
       </script>
   
   </td>

    <th>{{mb_label object=$filter field="_date_max"}}</th>
    <td class="date">{{mb_field object=$filter field="_date_max" form="filterFrm" register=true}} </td>
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
    <th colspan="5" class="title">
      {{if $list|@count > 0}}
      Historique de {{$item}}
      {{else}}
      Pas d'historique
      {{/if}}
    </th>
  </tr>
  {{/if}}
  <tr>
    {{if !$dialog}}
    <th>{{mb_title class=CUserLog field=object_class}}</th>
    <th>{{mb_title class=CUserLog field=object_id}}</th>
    {{/if}}
    <th>{{mb_title class=CUserLog field=user_id}}</th>
    <th colspan="2">{{mb_title class=CUserLog field=date}}</th>
    <th>{{mb_title class=CUserLog field=type}}</th>
    <th>{{mb_title class=CUserLog field=fields}}</th>
  </tr>
  {{foreach from=$list item=_log}}
  <tr>
    {{if !$dialog}}
    <td>{{$_log->object_class}}</td>
    <td>{{$_log->_ref_object->_view}} ({{$_log->object_id}})</td>
    {{/if}}
    <td style="text-align: center;">{{mb_ditto name=user value=$_log->_ref_user->_view}}</td>
    <td style="text-align: center;">{{mb_ditto name=date value=$_log->date|date_format:$dPconfig.date}}</td>
    <td style="text-align: center;">{{$_log->date|date_format:$dPconfig.time}}</td>
    <td>{{mb_value object=$_log field=type}}</td>
    <td>
      {{foreach from=$_log->_fields item=curr_field}}
      <label title="{{$curr_field}}">{{tr}}{{$_log->object_class}}-{{$curr_field}}{{/tr}}</label><br />
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>