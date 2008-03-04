<ul>
  {{foreach from=$elements item=element}}
    <li>
      <small style="display: none;">{{$element->_id}}</small>
      {{$element->libelle}}
    </li>
  {{/foreach}}
</ul>