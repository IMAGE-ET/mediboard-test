<form action="?" method="post" name="editLineTraitement-{{$line->_id}}">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_traitement_aed" />
  <input type="hidden" name="prescription_line_id" value="{{$line->_id}}"/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription_reelle->_id}}" />
  <input type="hidden" name="type" value="{{$prescription_reelle->type}}" />
  {{mb_field object=$line field=_traitement typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
  {{mb_label object=$line field=_traitement typeEnum="checkbox"}}
</form>