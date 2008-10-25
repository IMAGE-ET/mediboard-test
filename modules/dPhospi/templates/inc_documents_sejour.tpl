{{* $Id: $
  * Manipulation des documents d'un séjour et des interventions associées
  * @param $sejour CSejour
  *}}

<h1>{{$sejour->_view}}</h1>

<div id="Documents-{{$sejour->_guid}}" style="float: left; width: 50%;">
	<script type="text/javascript">
	Document.register('{{$sejour->_id}}','{{$sejour->_class_name}}','{{$sejour->praticien_id}}', 'Documents-{{$sejour->_guid}}', 'normal');
	</script>
</div>

<div id="Files-{{$sejour->_guid}}" style="float: left; width: 50%;">
<script type="text/javascript">
  File.register('{{$sejour->_id}}','{{$sejour->_class_name}}', "Files-{{$sejour->_guid}}");
</script>
</div>

{{foreach from=$sejour->_ref_operations item=operation}}
<h2 style="clear: both;">{{tr}}COperation{{/tr}} du {{$operation->_datetime|date_format:$dPconfig.date}}</h2>
<div id="Documents-{{$operation->_guid}}" style="float: left; width: 50%;">
	<script type="text/javascript">
	Document.register('{{$operation->_id}}','{{$operation->_class_name}}','{{$operation->chir_id}}', 'Documents-{{$operation->_guid}}', 'normal');
	</script>
</div>
<div id="Files-{{$operation->_guid}}" style="float: left; width: 50%;">
<script type="text/javascript">
  File.register('{{$operation->_id}}','{{$operation->_class_name}}', "Files-{{$operation->_guid}}");
</script>
</div>

{{/foreach}}
