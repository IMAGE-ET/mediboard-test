<table>
  <tr>
    <td>
      <ul>
      {{foreach from=$logs item="log"}}
        <li>
          {{tr}}{{$log->type}}{{/tr}} le {{$log->date|date_format:"%d/%m/%Y à %Hh%M"}}
          <br />
          par {{$log->_ref_user->_view}}
          </li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
</table>