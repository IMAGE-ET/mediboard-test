<table class="main tbl">
  <tr>
    <th>{{tr}}CDMI{{/tr}}</th>
    <th>{{tr}}CDMI-code{{/tr}}</th>
  </tr>
  
  {{foreach from=$products item=_product}}
    <tr>
      <td>{{$_product}}</td>
      <td>{{$_product->code}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2"><em>Aucun DMI retrouvé</em></td>
    </tr>
  {{/foreach}}
  
</table>