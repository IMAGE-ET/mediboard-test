<button type="button" style="width: 3em;" class="rtl right{{if !$object->_nb_files_docs}}-disabled{{/if}}" 
  onclick="setObject( {
    objClass: '{{$object->_class_name}}', 
    keywords: '', 
    id: {{$object->_id}}, 
    view: '{{$object->_view|smarty:nodefaults|JSAttribute}}' })"
  title="{{$object->_nb_files_docs}} {{tr}}CDocumentItem{{/tr}}">
  {{$object->_nb_files_docs}}
</button>
