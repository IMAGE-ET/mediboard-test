<table class="tbl">
  <tr>
    <th>{{mb_colonne class="CFunctions" field="text" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}</th>
    <th>{{mb_colonne class="CFunctions" field="type" order_col=$order_col order_way=$order_way url="?m=$m&tab=$tab"}}</th>
    <th class="narrow" colspan="2">{{tr}}CFunctions-back-users{{/tr}}</th>
  </tr>
  {{foreach from=$functions item=_function}}
  <tr {{if $_function->_id == $function_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="#{{$_function->_guid}}" onclick="showFunction('{{$_function->_id}}', this)">
        {{$_function->text}}
      </a>
    </td>
    <td>
      {{tr}}CFunctions.type.{{$_function->type}}{{/tr}}
    </td>
    <td style="text-align: right; background: #{{$_function->color}}">
      {{$_function->_count.users|nozero}}
    </td>
    <td style="text-align: right; background: #{{$_function->color}}">
      {{$_function->_count.secondary_functions|nozero}}
    </td>
  </tr>
  {{/foreach}}
</table>