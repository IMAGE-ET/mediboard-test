<table class="tbl">
  <tr>
    <th>Etablissement - {{$total_functions}} fonction(s)</th>
    <th>Type</th>
    <th>Utilisateurs</th>
  </tr>
  {{foreach from=$functions item=_function}}
  <tr {{if $_function->_id == $userfunction->_id}}class="selected"{{/if}}>
    <td>
      <a href="#" onclick="showFunction('{{$_function->_id}}', this)">
        {{$_function->text}}
      </a>
    </td>
    <td>
      {{tr}}CFunctions.type.{{$_function->type}}{{/tr}}
    </td>
    <td style="background: #{{$_function->color}}">
      <a href="#" onclick="showFunction('{{$_function->_id}}', this)">
        {{$_function->_ref_users|@count}}
      </a>
    </td>
  </tr>
  {{/foreach}}
</table>