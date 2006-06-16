<select name="sejour_id">
  <option value="0" {{if !$sejour_id}} selected="selected" {{/if}}>
    &mdash Selectionner un séjour existant
  </option>
  {{foreach from=$sejours item=curr_sejour}}
  <option value="{{$curr_sejour->sejour_id}}" {{if $sejour_id == $curr_sejour->sejour_id}} selected="selected" {{/if}}>
    {{$curr_sejour->_view}}
  </option>
  {{/foreach}}
</select>