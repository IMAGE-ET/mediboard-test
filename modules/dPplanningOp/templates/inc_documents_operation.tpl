{{* $Id$
  * Manipulation des documents d'une intervention et de son séjour associé
  * @param $operation COperation
  * @param $modelesByOwner array('COperation' => array(), 'CSejour' => array())
  *}}

{{if @$preloaded}}
  {{assign var=patient_id value=$operation->_ref_sejour->patient_id}}
  {{assign var=object value=$operation}}
  <div class="documentsV2-{{$object->_guid}} patient-{{$patient_id}} praticien-{{$object->chir_id}}" style="min-width: 200px; min-height: 50px; float: left; width: 50%;">
    {{mb_include module=patients template=inc_widget_documents}}
  </div>
  
  {{assign var=object value=$operation->_ref_sejour}}
  <div class="documentsV2-{{$object->_guid}} patient-{{$patient_id}} praticien-{{$object->praticien_id}}" style="min-width: 200px; min-height: 50px; float: left; width: 50%;">
    {{mb_include module=patients template=inc_widget_documents}}
  </div>
{{else}}
  {{assign var=object value=$operation}}
  <div style="float: left; width: 50%;" id="Documents-{{$object->_guid}}">
    <script type="text/javascript">
    Document.register('{{$object->_id}}','{{$object->_class}}','{{$object->chir_id}}', 'Documents-{{$object->_guid}}', 'collapse');
    </script>
  </div>
  
  {{assign var=object value=$operation->_ref_sejour}}
  <div style="float: left; width: 50%;" id="Documents-{{$object->_guid}}">
    <script type="text/javascript">
    Document.register('{{$object->_id}}','{{$object->_class}}','{{$object->praticien_id}}', 'Documents-{{$object->_guid}}', 'collapse');
    </script>
  </div>
{{/if}}