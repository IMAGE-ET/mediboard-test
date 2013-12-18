<table class="tbl">
  <tr>
    <td colspan="4"> <div class="small-info">Il y a {{$nb_operations}} intervention(s) ayant un score ASA à 1</div></td>
  </tr>
  <tr>
    <th>{{tr}}COperation{{/tr}}</th>
    <th>{{tr}}COperation-_patient_id{{/tr}}</th>
    <th>{{tr}}COperation-_prat_id{{/tr}}</th>
    <th>{{tr}}COperation-ASA{{/tr}}</th>
  </tr>

  {{foreach from=$operations item=operation}}
    <tr>
      <td>{{$operation->_view}}</td>
      <td>{{$operation->_ref_patient->_view}}</td>
      <td>{{$operation->_ref_praticien->_view}}</td>
      <td>{{$operation->ASA}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="4">{{tr}}COperation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  {{if $operations|@count < $nb_operations}}
    <tr>
      <td colspan="4">...</td>
    </tr>
  {{/if}}
</table>