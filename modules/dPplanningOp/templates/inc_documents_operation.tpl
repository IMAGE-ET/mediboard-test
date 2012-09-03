{{* $Id$
  * Manipulation des documents d'une intervention et de son séjour associé
  * @param $operation COperation
  * @param $modelesByOwner array('COperation' => array(), 'CSejour' => array())
  *}}

{{if @$preloaded}}
  {{assign var=object value=$operation}}
  <div class="documents-{{$object->_guid}} praticien-{{$object->chir_id}} mode-collapse" style="min-width: 200px; min-height: 50px; float: left; width: 50%;">
    {{mb_include module=compteRendu template=inc_widget_documents mode=collapse}}
  </div>
  
  {{assign var=object value=$operation->_ref_sejour}}
  <div class="documents-{{$object->_guid}} praticien-{{$object->praticien_id}} mode-collapse" style="min-width: 200px; min-height: 50px; float: left; width: 50%;">
    {{mb_include module=compteRendu template=inc_widget_documents mode=collapse}}
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