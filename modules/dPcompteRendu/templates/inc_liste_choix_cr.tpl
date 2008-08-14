{{if $lists|@count}}
<ul>
  {{foreach from=$lists item=curr_list}}
  <li>
    <select name="_liste{{$curr_list->liste_choix_id}}">
      <option value="undef">&mdash; {{$curr_list->nom}} &mdash;</option>
      {{foreach from=$curr_list->_valeurs|smarty:nodefaults item=curr_valeur}}
      <option>{{$curr_valeur}}</option>
      {{/foreach}}
    </select>
  </li>
  {{/foreach}}
  <li>
    <button class="tick notext" type="submit">{{tr}}Save{{/tr}}</button>
  </li>
</ul>
{{/if}}