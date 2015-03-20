<form name="editBacterio" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{{$op->_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$op field=flacons_bacterio}}</th>
      <td>{{mb_field object=$op field=flacons_bacterio form=editBacterio prop="num min|0" size=2 increment=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$op field=labo_bacterio}}</th>
      <td>{{mb_field object=$op field=labo_bacterio form=editBacterio autocomplete="true,1,50,true,true"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$op field=description_bacterio}}</th>
      <td>{{mb_field object=$op field=description_bacterio form="editBacterio" aidesaisie="validateOnBlur: 0, width: '100%'"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="save" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete : Control.Modal.close})">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>
