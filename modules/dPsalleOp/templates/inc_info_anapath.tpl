<form name="editAnapath" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  <input type="hidden" name="operation_id" value="{{$op->_id}}" />
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$op field=flacons_anapath}}</th>
      <td>{{mb_field object=$op field=flacons_anapath form=editAnapath prop="num min|0" size=2 increment=true}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$op field=labo_anapath}}</th>
      <td>{{mb_field object=$op field=labo_anapath form=editAnapath autocomplete="true,1,50,true,true"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$op field=description_anapath}}</th>
      <td>{{mb_field object=$op field=description_anapath form="editAnapath" aidesaisie="validateOnBlur: 0, width: '100%'"}}</td>
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
