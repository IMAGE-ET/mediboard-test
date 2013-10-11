{{mb_default var=section value=false}}

<table class="main tbl">
  {{if $object->_ref_documents_by_cat|@count}}
    <tr>
      <th class="{{$section|ternary:"section":"title"}}">Documents</th>
    </tr>
    {{mb_include module=files template=inc_embed_document_items_rows document_items=$object->_ref_documents_by_cat}}
  {{/if}}

  {{if $object->_ref_files_by_cat|@count}}
    <tr>
      <th class="{{$section|ternary:"section":"title"}}">Fichiers</th>
    </tr>
    {{mb_include module=files template=inc_embed_document_items_rows document_items=$object->_ref_files_by_cat}}
  {{/if}}
</table>
