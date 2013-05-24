{{* $Id$ *}}

{{mb_script module="dPcompteRendu" script="document"}}

<script type="text/javascript">
	
markAsSelected = function(anchor) {
  if (anchor) {
    $(anchor).up('tr').addUniqueClassName('selected');
  }
};

{{if $patient->_id}}
Main.add(function(){
	reloadPatient('{{$patient->_id}}', 0);
});
{{/if}}
	
</script>

<table class="main">
  <tr>
    <td class="halfPane">
      {{mb_include template="inc_list_patient"}}
    </td>
    <td class="halfPane" id="vwPatient">
      <div class="small-info">
      	Veuillez sélectionner un patient sur la gauche pour pouvoir le visualiser
      </div>
		</td>
  </tr>
</table>