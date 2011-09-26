{{mb_default var="view_prescription" value=1}}

<script type="text/javascript">
function submitTech(oForm) {
  onSubmitFormAjax(oForm, { onComplete : reloadListTech });
  if ($V(oForm.elements.del)) {
    oForm.reset();
  }
	return false;
}

function reloadListTech() {
  var UrllistTech = new Url("dPcabinet", "httpreq_vw_list_techniques_comp");
  UrllistTech.addParam("selConsult", document.editFrmFinish.consultation_id.value);
  UrllistTech.requestUpdate('listTech');
}

Main.add(function () {
  var oOpForm = getForm("editOpAnesthFrm");
  if(oOpForm){
  new AideSaisie.AutoComplete(oOpForm.rques, {
            objectClass: "COperation",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
  }
  var oAnesthForm = getForm("editInfosAnesthFrm");
  new AideSaisie.AutoComplete(oAnesthForm.prepa_preop, {
            objectClass: "CConsultAnesth",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
  if(oAnesthForm.premedication) {
    new AideSaisie.AutoComplete(oAnesthForm.premedication, {
              objectClass: "CConsultAnesth",
              timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
              validateOnBlur:0
            });
  }
  var oTechCompForm = getForm("addEditTechCompFrm");
  new AideSaisie.AutoComplete(oTechCompForm.technique, {
            objectClass: "CTechniqueComp",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
  var oRquesConsultForm = getForm("editRquesConsultFrm");
  new AideSaisie.AutoComplete(oRquesConsultForm.rques, {
            objectClass: "CConsultation",
            timestamp: "{{$conf.dPcompteRendu.CCompteRendu.timestamp}}",
            validateOnBlur:0
          });
});

</script>

{{assign var=operation value=$consult_anesth->_ref_operation}}

<table class="form">
  <tr>
    <td>
      <fieldset>
        <legend>Intervention</legend>
        <table class="layout main">
          <tr>
            <td class="halfPane">
              {{if $operation->_id}}
              <form name="editOpAnesthFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              {{mb_key object=$operation}}
              {{mb_label object=$operation field="rques"}}
              {{mb_field object=$operation field="rques" rows="4" onblur="this.form.onsubmit()"}}
              </form>
              {{else}}
              <div class="small-info text">
                Aucune intervention n'étant selectionné, vous ne pouvez pas accéder
                à la totalité des champs disponibles pour la consultation
              </div>
              {{/if}}
            </td>
            <td class="halfPane">
              {{if $operation->_id}}
                <form name="editTypeAnesthFrm" action="?m=dPcabinet" method="post" onsubmit="return onSubmitFormAjax(this);">
                  <input type="hidden" name="m" value="dPplanningOp" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="dosql" value="do_planning_aed" />
                  {{mb_key object=$operation}}
                  {{mb_label object=$operation field=type_anesth}}
                  {{mb_field object=$operation field=type_anesth options=$anesth style="width: 12em;" onchange="this.form.onsubmit()"}}
                </form>
                <br />
              {{/if}}
              <form name="editInfosASAFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
                <input type="hidden" name="m" value="dPcabinet" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
                {{mb_key object=$consult_anesth}}
                {{mb_label object=$consult_anesth field="ASA" style="padding-left: 6em;"}}
                {{mb_field object=$consult_anesth field="ASA" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
                <br />
                {{mb_label object=$consult_anesth field="position" style="padding-left: 4.5em;"}}
                {{mb_field object=$consult_anesth field="position" emptyLabel="Choose" style="width: 12em;" onchange="this.form.onsubmit()"}}
              </form>
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <legend>Pré-opératoire</legend>
        <form name="editInfosAnesthFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
	        <input type="hidden" name="m" value="dPcabinet" />
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
	        {{mb_key object=$consult_anesth}}
	        <table class="layout main">
	          <tr>
	            <td class="halfPane">
	              {{mb_label object=$consult_anesth field="prepa_preop"}}
	              {{mb_field object=$consult_anesth field="prepa_preop" rows="4" onchange="this.form.onsubmit()"}}
	            </td>
	            <td class="halfPane">
	              {{if !$isPrescriptionInstalled || ($conf.dPcabinet.CConsultAnesth.view_premedication && $app->user_prefs.displayPremedConsult)}}
	                {{mb_label object=$consult_anesth field="premedication"}}
	                {{mb_field object=$consult_anesth field="premedication" rows="4" onchange="this.form.onsubmit()"}}
	              {{else}}
	                {{if $conf.dPcabinet.CPrescription.view_prescription}}
                    {{if $view_prescription}}
  	                  {{mb_label object=$consult_anesth field="premedication"}}
  	                  <br />
	                    <button class="tick" type="button" onclick="tabsConsultAnesth.setActiveTab('prescription_sejour')">Accéder à la prescription</button>
                    {{/if}}
	                {{else}}
	                  <div class="small-info">
	                    La saisie de la prémédication n'est actuellement pas active
	                  </div>
	                {{/if}}
	              {{/if}}
	            </td>
	          </tr>
	        </table>
        </form>
      </fieldset>
			
      <fieldset>
        <legend>{{mb_label object=$techniquesComp field="technique"}}</legend>
        <table class="layout main">
          <tr>
            <td class="halfPane">
				      <form name="addEditTechCompFrm" action="?m=dPcabinet" method="post" onsubmit="return submitTech(this)">
					      <input type="hidden" name="m" value="dPcabinet" />
					      <input type="hidden" name="del" value="0" />
					      <input type="hidden" name="dosql" value="do_technique_aed" />
					      {{mb_field object=$consult_anesth field="consultation_anesth_id" hidden=1}}
	              {{mb_field object=$techniquesComp field="technique" rows="4"}}
	              <button class="add" type="submit">{{tr}}Add{{/tr}}</button>
							</form>
            </td>
            <td class="halfPane text" id="listTech">
              {{mb_include module=dPcabinet template=inc_consult_anesth/techniques_comp}}
            </td>
          </tr>
        </table>
      </fieldset>
			
      <form name="editRquesConsultFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
  
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      {{mb_key object=$consult}}
      <fieldset>
        <legend>{{mb_label object=$consult field="rques"}}</legend>
        {{mb_field object=$consult field="rques" rows="4" onblur="this.form.onsubmit()"}}
      </fieldset>
      </form>
    </td>
  </tr>
</table>