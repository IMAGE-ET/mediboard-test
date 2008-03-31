<li>
  <strong>{{$elt->_ref_element_prescription->_view}}</strong>
  ({{$elt->_ref_element_prescription->_ref_category_prescription->_view}})
  {{if $elt->commentaire}}
  <em>{{$elt->commentaire}}</em>
  {{/if}}
</li>