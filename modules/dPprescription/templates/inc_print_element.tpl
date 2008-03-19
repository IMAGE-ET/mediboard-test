<ul>
  {{foreach from=$elements item="_element"}}
  <li>
    <strong>{{$_element->_ref_element_prescription->_view}}</strong>
    ({{$_element->_ref_element_prescription->_ref_category_prescription->_view}})
    {{if $_element->commentaire}}
    <em>{{$_element->commentaire}}</em>
    {{/if}}
  </li>
  {{/foreach}}
  {{foreach from=$commentaires item="_comment"}}
  <li>
    {{$_comment->commentaire}}
  </li> 
  {{/foreach}}
</ul>