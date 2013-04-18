<form name="editConfig-tarifs" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  
  <table class="form">
    <tr>
      <th class="category" colspan="2">{{tr}}CTarif{{/tr}}</th>
    </tr>
    
    {{assign var="class" value="Tarifs"}}
    {{mb_include module=system template=inc_config_bool var=show_tarifs_etab}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>