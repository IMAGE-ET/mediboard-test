<ul style="text-align: left;">

{{foreach from=$categories item=_category}}
  <li onclick="$V(this.up('div').up('div').next('input'), '{{$_category->_id}}');">
    <div>{{$_category->nom}}</div>
  </li>
{{/foreach}}

</ul>