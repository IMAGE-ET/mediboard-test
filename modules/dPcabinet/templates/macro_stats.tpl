<table class="tbl">
  <tr>
    <th class="title" rowspan="2">Praticien</th>
    <th class="title" colspan="30">Totaux par jour</th>
  </tr>
  <tr>
    {{if $period == "day"  }}{{assign var=format value="%d %a"}}{{/if}}
    {{if $period == "week" }}{{assign var=format value="%W"   }}{{/if}}
    {{if $period == "month"}}{{assign var=format value="%m"   }}{{/if}}
    
    {{foreach from=$dates item=_date}}
    <th class="text narrow" title="{{$_date}}">{{$_date|date_format:$format}}</th>
    {{/foreach}}
  </tr>
 
  {{foreach from=$totaux key=_user_id item=_dates}}
  <tr>
    <td>
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$users.$_user_id}}
    </td>
    {{foreach from=$dates item=_date}}     
    <td style="text-align: center;">
      {{if array_key_exists($_date, $_dates)}}
        <strong>{{$_dates.$_date}}</strong>
      {{else}}
        &ndash;
      {{/if}}
    {{/foreach}}
    </td>
  </tr>     
  {{foreachelse}}
  <tr><td class="empty" colspan=31">{{tr}}CConsultation.none{{/tr}}</td></tr>
  {{/foreach}}
</table>