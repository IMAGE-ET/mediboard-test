<script type="text/javascript">
  switchToConsult = function() {
    Control.Modal.close(); onSubmitFormAjax(getForm('addConsultation'));
  }
  toggleOperation = function(elt) {
    $V(getForm('addConsultation')._operation_id, elt.value);
    switchToConsult();
  }
</script>
<table class="form">
  {{foreach from=$operations item=_operation}}
    <tr>
      <td>
        <button class="add notext" value="{{$_operation->_id}}" onclick="toggleOperation(this);"></button>
        <strong>Intervention du {{$_operation->_datetime|date_format:$conf.date}}</strong>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td>
        <strong>{{tr}}COperation.none{{/tr}}</strong><br/>
        <button class="hslip" onclick="switchToConsult()">Passer à la consultation</button>
      </td>
    </tr>
  {{/foreach}}
</table>