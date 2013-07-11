{{mb_script module=facturation script=debiteur}}

<button type="button" class="new" onclick="Debiteur.edit('0');">{{tr}}CDebiteur-title-create{{/tr}}</button>
<table class="main tbl">
  <tr>
    <th colspan="7" class="title">{{tr}}CDebiteur.all{{/tr}}</th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class= CDebiteur field=numero}}</th>
    <th>{{mb_title class= CDebiteur field=nom}}</th>
    <th>{{mb_title class= CDebiteur field=description}}</th>
  </tr>
  {{foreach from=$debiteurs item=debiteur}}
    <tr style="text-align:center;">
      <td><a href="#" onclick="Debiteur.edit('{{$debiteur->_id}}');">{{mb_value object=$debiteur field=numero}}</a></td>
      <td>{{mb_value object=$debiteur field=nom}}</td>
      <td>{{mb_value object=$debiteur field=description}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">{{tr}}CDebiteur.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>