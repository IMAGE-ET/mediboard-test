{{if $long_period}}
  <div class="big-warning">{{tr}}CCompteRendu-alert_long_period{{/tr}}</div>
{{/if}}

{{mb_include module=system template=inc_pagination total=$total_docs current=$page change_page="changePage" step=30}}
<table class="tbl">
  <tr>
    <th class="title" colspan="2">
      Liste des documents <small>({{$total_docs}})</small>
    </td>
  </tr>  
  <tr>
    <th>{{mb_label class=CCompteRendu field=nom}}</th>
    <th>{{mb_label class=CCompteRendu field=_date}}</th>
  </tr>
  {{foreach from=$docs item=_doc}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_doc->_guid}}')">
          {{$_doc}}
        </span>
      </td>
      <td>
        {{mb_value object=$_doc field=_date}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">{{tr}}CCompteRendu.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>