<script type="text/javascript">
	
Main.add(function(){
  ViewPort.SetAvlHeight("planning-technicien", 1);
  var height = $('planning-technicien').getDimensions().height - 50;
  PlanningTechnicien.show('{{$kine_id}}', null, null, height, true, true);
});
	
</script>

{{if $count_evts}}
<div class="small-warning">
	 {{$count_evts}} evenement(s) non validé(s) la semaine précédente.
</div>	
{{/if}}

<div style="position: relative">
  <div style="position: absolute; top: 0px; left: 3em;">
    <button type="button" class="tick singleclick"          onclick="ModalValidation.set({realise: '1'}); ModalValidation.update();">{{tr}}Validate{{/tr}}</button>
    <button type="button" class="cancel notext singleclick" onclick="ModalValidation.set({realise: '0'}); ModalValidation.submit();">{{tr}}Cancel{{/tr}}</button>
  </div>
  <div style="position: absolute; top: 0px; right: 0px;">
    <button type="button" class="print notext" onclick="PlanningTechnicien.print();">{{tr}}Print{{/tr}}</button>
    <button type="button" class="change notext" onclick="PlanningTechnicien.toggle();">{{tr}}Change{{/tr}}</button>
  </div>
  <div id="planning-technicien"></div>
</div>