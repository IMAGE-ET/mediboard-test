<script type="text/javascript">

function newOperation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("operation_id", 0);
  url.addParam("sejour_id", 0);
  url.redirect();
}

function newHospitalisation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_sejour");
  url.addParam("praticien_id", chir_id);
  url.addParam("patient_id", pat_id);
  url.addParam("sejour_id", 0);
  url.redirect();
}

function newConsultation(chir_id, pat_id, consult_urgence_id) {
  var url = new Url;
  url.setModuleTab("dPcabinet", "edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("consult_urgence_id", consult_urgence_id);
  url.addParam("consultation_id", 0);
  url.redirect();
}

</script>

<table class="form">
  <tr>
    <th class="category">
      <div style="float:left;" class="noteDiv CPatient-{{$consult->patient_id}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
			{{if $patient->_id_vitale}}
      <div style="float:right;">
	      <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à une carte Vitale" />
      </div>
      {{/if}}
      Patient
    </th>
    <th class="category">
    	Correspondants
    	</th>
    <th class="category">
	    <div class="idsante400" id="{{$consult->_class_name}}-{{$consult->_id}}"></div>
      <a style="float:right;" href="#" onclick="view_log('{{$consult->_class_name}}',{{$consult->_id}})">
        <img src="images/icons/history.gif" alt="historique" />
      </a>
      Historique
    </th>
    <th class="category">Planification</th>
  </tr>
  
  <tr>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_infos.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_medecins.tpl"}}
    </td>
    <td class="text">
      {{include file="../../dPcabinet/templates/inc_patient_history.tpl"}}
    </td>
    <td class="button">
      {{if !$app->user_prefs.simpleCabinet}}
	      {{if @$modules.ecap->mod_active}}
	      {{mb_include_script module=ecap script=dhe}}
	      <div id="dhe"></div>
	      <script type="text/javascript">DHE.register("{{$consult->patient_id}}", "{{$consult->_praticien_id}}", "dhe");</script>
	      {{else}}
	      <button style="margin: 1px;" class="new" type="button" onclick="newOperation      ({{$consult->_praticien_id}},{{$consult->patient_id}})">Nouvelle intervention</button>
	      <br/>
	      <button style="margin: 1px;" class="new" type="button" onclick="newHospitalisation({{$consult->_praticien_id},{{$consult->patient_id}})">Nouveau séjour</button>
	      <br/>
	    	{{/if}}
    	{{/if}}
    	
      <button style="margin: 1px;" class="new" type="button" onclick="newConsultation   ({{$consult->_praticien_id}},{{$consult->patient_id}})">Nouvelle consultation</button>
    </td>
  </tr>
</table>
