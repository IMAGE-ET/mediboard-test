<h2>
  File <code>{{$file|ide}}</code>:
  {{$errors.numErrors}} errors, 
  {{$errors.numWarnings}} warnings
</h2>

<table class="tbl">
  <tr>
    <th>Line</th>
    <th>Col</th>
    <th>Message</th>
    <th>Source</th>
    <th title="Severity">S</th>
  </tr>

{{foreach from=$alerts item=_alert}}
  <tr>
    <td style="text-align: right; font-weight: bold;">{{$file|ide:$_alert.line:$_alert.line}}</td>
    <td style="text-align: right;">{{$_alert.column}}</td>
    <td class="text">{{$_alert.message}}</td>
    <td class="text">{{$_alert.source|replace:".":" > "}}</td>
    <td class="{{$_alert.type}}">{{$_alert.severity}}</td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="5" class="empty">{{tr}}CAlert.none{{/tr}}</td>
  </tr>
{{/foreach}}

</table>