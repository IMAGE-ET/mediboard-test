<button type="button" style="width: 3em; display: block" id="docItem_{{$object->_guid}}" class="rtl right{{if !$object->_nb_files_docs}}-disabled{{/if}}"
  onclick="setObject( {
    objClass: '{{$object->_class}}', 
    keywords: '', 
    id: {{$object->_id}}, 
    view: '{{$object->_view|smarty:nodefaults|JSAttribute}}' }); ViewFullPatient.select(this.up('tr').down('a'));"
  title="{{$object->_nb_files_docs}} {{tr}}CDocumentItem{{/tr}}">
  {{$object->_nb_files_docs}}
</button>
