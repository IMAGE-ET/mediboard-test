{{* $Id: $
  * Manipulation des documents d'un séjour et des interventions associées
  * @param $sejour CSejour
  *}}

<h1>{{$sejour->_view}}</h1>

<div id="Documents-{{$sejour->_guid}}">
	<script type="text/javascript">
	Document.register('{{$sejour->_id}}','{{$sejour->_class_name}}','{{$sejour->praticien_id}}', 'Documents-{{$sejour->_guid}}', 'normal');
	</script>
</div>

{{foreach from=$sejour->_ref_operations item=operation}}
<h2>{{tr}}COperation{{/tr}} du {{$operation->_datetime|date_format:$dPconfig.date}}</h2>
<div id="Documents-{{$operation->_guid}}">
	<script type="text/javascript">
	Document.register('{{$operation->_id}}','{{$operation->_class_name}}','{{$operation->chir_id}}', 'Documents-{{$operation->_guid}}', 'normal');
	</script>
</div>
{{/foreach}}
