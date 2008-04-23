<!-- Creation d'une ligne avec des dates contiguës -->
<form name="addLineCont-{{$line->_id}}" method="post" action="">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_add_line_contigue_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_line_id" value="{{$line->_id}}" />
  <input type="hidden" name="mode_pharma" value="{{$mode_pharma}}" />
  <button type="button" class="new" onclick="submitFormAjax(document.forms['addLineCont-{{$line->_id}}'],'systemMsg')">Ajouter une ligne</button>
</form>