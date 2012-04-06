<table class="tbl">
  <tr>
    <th class="category">
      Plages vides qui seront supprimées
    </th>
  </tr>
  {{foreach from=$plages item=_plage}}
    <tr>
      <td>{{$_plage}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}CPlageOp.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>
