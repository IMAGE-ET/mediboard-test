<ul>
  {{foreach from=$elements item=element}}
    <li>
      <small style="display: none;">{{$element->_id}}</small>
      <small style="display: none;">{{$element->_ref_category_prescription->chapitre}}</small>
      <strong>{{$element->_ref_category_prescription->_view}}</strong> :
      {{$element->libelle|lower|replace:$libelle:"<em>$libelle</em>"}}
    </li>
  {{/foreach}}
</ul>