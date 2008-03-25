<ul>
  {{foreach from=$elements item=element}}
    <li>
      <small style="display: none;">{{$element->_id}}</small>
      <small style="display: none;">{{$element->_ref_category_prescription->chapitre}}</small>
      {{$element->libelle}} [{{$element->_ref_category_prescription->_view}}]
    </li>
  {{/foreach}}
</ul>