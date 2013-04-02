<form name="editConfigProtocole" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />

  {{assign var="class" value="CProtocole"}}
  <table class="form">
    <tr>
      <th class="title" colspan="2">Général</th>
    </tr>

    {{mb_include module=system template=inc_config_bool var=nicer}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>