{{*
 * $Id$
 *
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="prescription_editor"}}
  {{mb_script module="prescription" script="element_selector"}}
{{/if}}

{{mb_script module="compteRendu" script="document"}}
{{mb_script module="compteRendu" script="modele_selector"}}
{{mb_script module="cabinet"     script="edit_consultation"}}

<script>
  {{if !$consult->_canEdit}}
    App.readonly = true;
  {{/if}}

  reloadConsultAnesth = function() {
    var sejour_id = document.addOpFrm.sejour_id.value;

    // Mise a jour du sejour_id
    DossierMedical.updateSejourId(sejour_id);

    // refresh de la liste des antecedents du sejour
    DossierMedical.reloadDossierPatient();
    DossierMedical.reloadDossierSejour();

    // Reload Intervention
    loadIntervention();

    // Reload Infos Anesth
    loadInfosAnesth();

    Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);

    if($('facteursRisque')){
      refreshFacteursRisque();
    }
  };

  submitAll = function() {
    var oForm;
    oForm = getForm("editFrmIntubation");
    if(oForm) {
      onSubmitFormAjax(oForm);
    }
    oForm = getForm("editExamCompFrm");
    if(oForm) {
      onSubmitFormAjax(oForm);
    }
    oForm = getForm("editFrmExams");
    if(oForm) {
      onSubmitFormAjax(oForm);
    }
  };

  submitOpConsult = function() {
    onSubmitFormAjax(getForm("addOpFrm"), { onComplete: reloadConsultAnesth } );
  };

  reloadDiagnostic = function(sejour_id, modeDAS) {
    var url = new Url("salleOp", "httpreq_diagnostic_principal");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modeDAS", modeDAS);
    url.requestUpdate("cim");
  };

  view_history_consult = function(id) {
    var url = new Url("cabinet", "vw_history");
    url.addParam("consultation_id", id);
    url.popup(600, 500, "consult_history");
  };

  submitForm = function(oForm) {
    onSubmitFormAjax(oForm);
  };

  printAllDocs = function() {
    var url = new Url('cabinet', 'print_select_docs');
    url.addElement(document.editFrmFinish.consultation_id);
    if(DossierMedical.sejour_id) {
      url.addParam("sejour_id", DossierMedical.sejour_id);
    }
    url.popup(700, 500, "printDocuments");
  };

  printFiche = function() {
    var url = new Url("cabinet", "print_fiche");
    url.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
    url.addParam("print", true);
    url.popup(700, 500, "printFiche");
  };
</script>

{{if $consult->_id}}
  {{assign var="patient" value=$consult->_ref_patient}}
  {{if $consult_anesth}}
    {{mb_include module=cabinet template=inc_consult_anesth/patient_infos}}
  {{/if}}
  <div id="finishBanner">
    {{mb_include module=cabinet template=inc_finish_banner}}
  </div>
  {{if $consult_anesth}}
    {{mb_include module=cabinet template=inc_consult_anesth/accord}}
  {{else}}
    {{mb_include module=cabinet template=inc_patient_infos_accord_consult}}
    {{mb_include module=cabinet template=acc_consultation}}
  {{/if}}
{{/if}}