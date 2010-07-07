<script type="text/javascript">
	
Main.add(function(){
  PlanningTechnicien.show('{{$kine_id}}', null, null, 650, true, true);
});
	
</script>

{{if $count_evts}}
<div class="small-warning">
	 {{$count_evts}} evenement(s) non validé(s) la semaine précédente.
</div>	
{{/if}}

<div style="position: relative">
  <div style="position: absolute; top: 0px; left: 3em;">
    <button type="button" class="tick" onclick="$V(oFormSelectedEvents.realise, '1'); updateSelectedEvents();">{{tr}}Validate{{/tr}}</button>
    <button type="button" class="cancel notext" onclick="updateSelectedEvents(); submitValidation(oFormSelectedEvents);">{{tr}}Cancel{{/tr}}</button>
  </div>
  <div style="position: absolute; top: 0px; right: 0px;">
    <button type="button" class="change notext" onclick="PlanningTechnicien.toggle();"></button>
  </div>
  <div id="planning-technicien"></div>
</div>