{{* $Id$
  * Manipulation des documents d'un séjour et des interventions associées
  * @param $sejour CSejour
  *}}

<h1>{{$sejour}}</h1>

{{if !$operation_id}}
  <div id="Documents-{{$sejour->_guid}}" style="float: left; width: 50%;">
    <script>
    Document.register('{{$sejour->_id}}','{{$sejour->_class}}','{{$sejour->praticien_id}}', 'Documents-{{$sejour->_guid}}', 'normal');
    </script>
  </div>
  
  <div id="Files-{{$sejour->_guid}}" style="float: left; width: 50%;">
    <script>
      File.register('{{$sejour->_id}}','{{$sejour->_class}}', "Files-{{$sejour->_guid}}");
    </script>
  </div>
{{/if}}

{{if !$only_sejour}}
  {{if !$operation_id && $sejour->_ref_consult_anesth->_id}}
    {{assign var=consult_anesth value=$sejour->_ref_consult_anesth}}
    {{assign var=consult value=$consult_anesth->_ref_consultation}}
    <h2 style="clear: both;">{{tr}}CConsultAnesth{{/tr}} du {{$consult_anesth->_date_consult|date_format:$conf.date}}</h2>
    <div id="Documents-{{$consult_anesth->_guid}}" style="float: left; width: 50%;">
      <script>
        Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class}}','{{$consult_anesth->chir_id}}', 'Documents-{{$consult_anesth->_guid}}', 'normal');
      </script>
    </div>

    <div id="Files-{{$consult->_guid}}" style="float: left; width: 50%;">
      <script>
        File.register('{{$consult->_id}}','{{$consult->_class}}', "Files-{{$consult->_guid}}");
      </script>
    </div>
  {{/if}}

  {{if !$operation_id && $sejour->_ref_consultations|@count}}
    {{foreach from=$sejour->_ref_consultations item=_consultation}}
      <h2 style="clear: both;">{{tr}}CConsultation{{/tr}} du {{$_consultation->_ref_plageconsult->date|date_format:$conf.date}}</h2>
      <div id="Documents-{{$_consultation->_guid}}" style="float: left; width: 50%;">
        <script>
          Document.register('{{$_consultation->_id}}','{{$_consultation->_class}}','{{$_consultation->_ref_plageconsult->chir_id}}', 'Documents-{{$_consultation->_guid}}', 'normal');
        </script>
      </div>
      <div id="Files-{{$_consultation->_guid}}" style="float: left; width: 50%;">
        <script>
          File.register('{{$_consultation->_id}}','{{$_consultation->_class}}', "Files-{{$_consultation->_guid}}");
        </script>
      </div>
    {{/foreach}}
  {{/if}}

  {{foreach from=$sejour->_ref_operations item=operation}}
    <h2 style="clear: both;">{{tr}}COperation{{/tr}} du {{$operation->_datetime|date_format:$conf.date}}</h2>
    <div id="Documents-{{$operation->_guid}}" style="float: left; width: 50%;">
      <script>
      Document.register('{{$operation->_id}}','{{$operation->_class}}','{{$operation->chir_id}}', 'Documents-{{$operation->_guid}}', 'normal');
      </script>
    </div>
    <div id="Files-{{$operation->_guid}}" style="float: left; width: 50%;">
      <script>
        File.register('{{$operation->_id}}','{{$operation->_class}}', "Files-{{$operation->_guid}}");
      </script>
    </div>
    
    {{if $operation->_ref_consult_anesth->_id && !$operation_id}}
      {{assign var=consult_anesth value=$operation->_ref_consult_anesth}}
      {{assign var=consult value=$consult_anesth->_ref_consultation}}
      <h2 style="clear: both;">{{tr}}CConsultAnesth{{/tr}} du {{$consult_anesth->_date_consult|date_format:$conf.date}}</h2>
      <div id="Documents-{{$consult_anesth->_guid}}" style="float: left; width: 50%;">
        <script>
        Document.register('{{$consult_anesth->_id}}','{{$consult_anesth->_class}}','{{$consult_anesth->chir_id}}', 'Documents-{{$consult_anesth->_guid}}', 'normal');
        </script>
      </div>
      <div id="Files-{{$consult->_guid}}" style="float: left; width: 50%;">
        <script>
          File.register('{{$consult->_id}}','{{$consult->_class}}', "Files-{{$consult->_guid}}");
        </script>
      </div>
    {{/if}}
  {{/foreach}}
{{/if}}

{{if $with_patient}}
  {{assign var=patient value=$sejour->_ref_patient}}
  <h1 style="clear: both;">{{$patient}}</h1>
  <div id="Documents-{{$patient->_guid}}" style="float: left; width: 50%;">
    <script>
      Document.register('{{$patient->_id}}','{{$patient->_class}}', null, 'Documents-{{$patient->_guid}}', 'normal');
    </script>
  </div>

  <div id="Files-{{$patient->_guid}}" style="float: left; width: 50%;">
    <script>
      File.register('{{$patient->_id}}','{{$patient->_class}}', "Files-{{$patient->_guid}}");
    </script>
  </div>
{{/if}}