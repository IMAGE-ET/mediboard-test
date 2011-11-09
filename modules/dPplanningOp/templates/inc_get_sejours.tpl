<script type="text/javascript">

Main.add( function(){
  var oForm = document.editOp;
  Sejour.sejours_collision = {{$sejours_collision|@json}};
  Sejour.preselectSejour(oForm._date.value);
} );

</script>

<select name="sejour_id" onchange="reloadSejour();">
  <option value="" selected="selected">
    &mdash; Selectionner un séjour existant
  </option>
  {{foreach from=$sejours item=_sejour}}
  <option value="{{$_sejour->sejour_id}}">
    {{$_sejour->_view}}
  </option>
  {{/foreach}}
</select>