<script type="text/javascript">
Main.add(function() {
  var oFormObs = getForm("editObs");
  if(oFormObs){
    new AideSaisie.AutoComplete(oFormObs.text, {
      objectClass: "CObservationMedicale", 
      timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
      validateOnBlur:0
    });
  }
});
</script>

<form name="editObs" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_observation_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="dPhospi" />
  {{mb_key object=$observation}}
  <input type="hidden" name="sejour_id" value="{{$observation->sejour_id}}" />
  <input type="hidden" name="user_id" value="{{$observation->user_id}}" />
  <input type="hidden" name="date" value="now" /> 
  <div style="text-align: left;">
    {{tr}}CObservationMedicale-degre{{/tr}} : {{mb_field object=$observation field="degre"}}
    <br />
  </div>
  {{mb_field object=$observation field="text"}}
  
  <button type="button" class="{{if $observation->_id}}save{{else}}add{{/if}}"
    onclick="submitSuivi(this.form);">
    {{if $observation->_id}}
      {{tr}}Save{{/tr}}
    {{else}}
      {{tr}}Add{{/tr}}
    {{/if}}
  </button> 
</form>