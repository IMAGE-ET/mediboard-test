<table class="tbl">
  <tr>
    <th>Recherche de code CIM10</th>
  </tr>
  <tr>
    <td>
      <input type="text" />
    </td>
  </tr>
  <tr>
    <th>Liste des codes</th>
  </tr>
  {{foreach from=$list item=_code}}
    <tr>
      <td>
        {{$_code->code}}
      </td>
    </tr>
  {{/foreach}}
</table>