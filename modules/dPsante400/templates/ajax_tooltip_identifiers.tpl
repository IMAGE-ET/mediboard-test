<table class="tbl">
  {{foreach from=$identifiers item=_identifier}}
  <tr>
    <td><strong>{{mb_value object=$_identifier field=tag}}</strong></td>
    <td>{{mb_value object=$_identifier field=id400}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CIdSante400.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
