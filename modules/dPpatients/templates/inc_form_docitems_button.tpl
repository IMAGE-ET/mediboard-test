{{math equation=x-y-z x=$object->_nb_files_docs y=$object->_nb_cancelled_files z=$object->_nb_cancelled_docs assign=nb_files_docs}}

<button type="button" style="width: 3em; display: block" id="docItem_{{$object->_guid}}" class="rtl right{{if !$nb_files_docs}}-disabled{{/if}} droppable"
  onclick="setObject( {
    objClass: '{{$object->_class}}', 
    keywords: '', 
    id: {{$object->_id}}, 
    view: '{{$object->_view|smarty:nodefaults|JSAttribute}}' }); ViewFullPatient.select(this.up('tr').down('a'));"
  title="{{$object->_nb_files_docs}} {{tr}}CDocumentItem{{/tr}}"
   data-guid="{{$object->_guid}}">
  {{$nb_files_docs}}
</button>