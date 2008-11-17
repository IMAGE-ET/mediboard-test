{{if !$dialog}}
{{include file=filter_history.tpl}}
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
  
  {{include file=inc_history_line.tpl logs=$list}}
</table>