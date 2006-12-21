<script type="text/javascript">

function newOperation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("operation_id", 0);
  url.redirect();
}

function newHospitalisation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPplanningOp", "vw_edit_sejour");
  url.addParam("praticien_id", chir_id);
  url.addParam("patient_id", pat_id);
  url.addParam("hospitalisation_id", 0);
  url.redirect();
}

function newConsultation(chir_id, pat_id) {
  var url = new Url;
  url.setModuleTab("dPcabinet", "edit_planning");
  url.addParam("chir_id", chir_id);
  url.addParam("pat_id", pat_id);
  url.addParam("consultation_id", 0);
  url.redirect();
}

var urlDHEParams = {{$urlDHEParams|@json}};

function newDHE(oForm) {
  {{if !$codePraticienEc || !$etablissements|@count}}
    alert("Vous n'êtes pas autorisé à créer une DHE");
  {{elseif !$patient->naissance || $patient->naissance == "0000-00-00"}}
    alert("Le patient n'a pas une date de naissance valide");
  {{else}}
    var url = new Url;
    url.addParam("codeClinique", oForm.etablissement.value);
    for(param in urlDHEParams) {
      if(param != "extends") {
        url.addParam(param, urlDHEParams[param]);
      }
    }
    url.popDirect("900", "600", "eCap", "{{$urlDHE|smarty:nodefaults}}")
  {{/if}}
}

</script>

      <table class="form">
        <tr>
          <th class="category">
            Patient
          </th>
          <th class="category">Correspondants</th>
          <th class="category">
            <a style="float:right;" href="#" onclick="view_log('CConsultation',{{$consult->consultation_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
            Historique
          </th>
          <th class="category">Planification</th>
        </tr>
        <tr>
          <td class="text">
            {{include file="inc_patient_infos.tpl"}}
          </td>
          <td class="text">
            {{include file="inc_patient_medecins.tpl"}}
          </td>
          <td class="text">
            {{include file="inc_patient_history.tpl"}}
          </td>
          <td class="button">
            <form name="newAction" action="" method="get">
            {{if $dPconfig.interop.mode_compat == "medicap"}}
            <button style="margin: 1px;" class="new" type="button" onclick="newDHE(this.form)">Nouvelle DHE</button>
            <br />
            <select name="etablissement">
              {{foreach from=$etablissements item=currEtablissement key=keyEtablissement}}
              <option value="{{$keyEtablissement}}" {{if $currEtablissement->group_id==$g}}selected="selected"{{/if}}>
                {{$currEtablissement->_view}}
              </option>
              {{/foreach}}
            </select>
            {{else}}
            <button style="margin: 1px;" class="new" type="button" onclick="newOperation      ({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouvelle intervention</button>
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newHospitalisation({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouveau séjour</button>
            {{/if}}
            <br/>
            <button style="margin: 1px;" class="new" type="button" onclick="newConsultation   ({{$consult->_ref_plageconsult->chir_id}},{{$consult->patient_id}})">Nouvelle consultation</button>
            </form>
          </td>
        </tr>
      </table>
