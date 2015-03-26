<table class="main tbl">
  <tr>
    <th>{{tr}}Total{{/tr}}</th>
    <th>{{tr}}Value{{/tr}}</th>
  </tr>

  {{foreach from=$counts item=_count}}
    <tr>
      <td>{{$_count.total}}</td>
      <td class="text">
        {{mb_include module=importTools template=inc_display_value value=$_count.value col_info=$columns.$column}}
      </td>
    </tr>
  {{/foreach}}
</table>

<div class="info">{{$counts|@count}} valeurs</div>