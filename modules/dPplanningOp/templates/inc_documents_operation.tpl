{{* $Id: $
  * Manipulation des documents d'une intervention et de son séjour associé
  * @param $operation COperation
  *}}

<div style="float: left; width: 50%;" id="Documents-{{$operation->_guid}}">
<script type="text/javascript">
Document.register('{{$operation->_id}}','{{$operation->_class_name}}','{{$operation->chir_id}}', 'Documents-{{$operation->_guid}}', 'collapse');
</script>
</div>

{{assign var=sejour value=$operation->_ref_sejour}}
<div style="float: left; width: 50%;" id="Documents-{{$sejour->_guid}}">
<script type="text/javascript">
Document.register('{{$sejour->_id}}','{{$sejour->_class_name}}','{{$sejour->praticien_id}}', 'Documents-{{$sejour->_guid}}', 'collapse');
</script>
</div>
