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

{{mb_default var=represcription value=0}}
{{assign var="chir_id"        value=$consult->_ref_plageconsult->_ref_chir->_id}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{assign var="module"         value="cabinet"}}
{{assign var="object"         value=$consult}}
{{mb_include module=salleOp template=js_codage_ccam}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="element_selector"}}
  {{mb_script module="prescription" script="prescription"}}
{{/if}}

{{mb_script module="cabinet" script="reglement"}}

<script>
  {{if $isPrescriptionInstalled && "dPcabinet CPrescription view_prescription"|conf:"CGroups-$g"}}
    function reloadPrescription(prescription_id) {
      Prescription.reloadPrescSejour(prescription_id, '', '1', null, null, null,'', null, false);
    }
  {{/if}}

  var constantesMedicalesDrawn = false;
  function refreshConstantesMedicales(force) {
    if (!constantesMedicalesDrawn || force) {
      var url = new Url("patients", "httpreq_vw_constantes_medicales");
      url.addParam("patient_id", {{$consult->_ref_patient->_id}});
      url.addParam("context_guid", "{{$consult->_guid}}");
      url.addParam("infos_patient", 1);
      if (window.oGraphs) {
        url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
      }
      url.requestUpdate("constantes-medicales");
      constantesMedicalesDrawn = true;
    }
  }

  function loadAntTrait() {
    var url = new Url("cabinet", "httpreq_vw_antecedents");
    {{if $consult->sejour_id && !$consult->_ref_consult_anesth->_ref_operation->_id}}
      url.addParam("sejour_id", "{{$consult->sejour_id}}");
    {{else}}
      url.addParam("sejour_id", "{{$consult->_ref_consult_anesth->_ref_operation->sejour_id}}");
    {{/if}}
    url.addParam("patient_id", "{{$consult->patient_id}}");
    url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
    url.addParam("show_header", 0);
    url.requestUpdate("AntTrait");
  }

  function refreshFacteursRisque() {
    var url = new Url("cabinet", "httpreq_vw_facteurs_risque");
    url.addParam("consultation_id", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
    url.requestUpdate("facteursRisque");
  }

  function loadActes() {
    var url = new Url("cabinet", "ajax_vw_actes");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.requestUpdate("Actes");
  }

  loadIntervention = function() {
    var consultUrl = new Url("cabinet", "httpreq_vw_consult_anesth");
    consultUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
    consultUrl.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
    consultUrl.addParam("represcription", '{{$represcription}}');
    consultUrl.requestUpdate('consultAnesth');
  }

  function loadInfosAnesth() {
    var infosAnesthUrl = new Url("cabinet", "httpreq_vw_choix_anesth");
    infosAnesthUrl.addParam("selConsult", document.editFrmFinish.consultation_id.value);
    infosAnesthUrl.addParam("dossier_anesth_id", document.editFrmFinish._consult_anesth_id.value);
    infosAnesthUrl.requestUpdate('InfoAnesth');
  }

  function loadDocs() {
    var url = new Url("cabinet", "ajax_vw_documents");
    url.addParam("consult_id", "{{$consult->_id}}");
    url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
    url.requestUpdate("fdrConsult");
  }

  function loadExams() {
    var url = new Url("cabinet", "ajax_vw_examens_anesth");
    url.addParam("dossier_anesth_id", "{{$consult->_ref_consult_anesth->_id}}");
    url.requestUpdate("Exams");
  }

  function loadResultLabo () {
    var url = new Url("Imeds", "httpreq_vw_patient_results");
    url.addParam("patient_id", "{{$consult->_ref_patient->_id}}");
    url.requestUpdate('labo');
  }

  Main.add(function () {
    tabsConsultAnesth = Control.Tabs.create('tab-consult-anesth', false, {
      afterChange: function (container) {
        switch (container.id) {
          case 'Intub':
            if (window.guessVentilation) {
              guessVentilation();
            }
            break;

          case 'InfoAnesth':
            if (window.guessScoreApfel) {
              guessScoreApfel();
            }
            break;
          case 'labo' :
            loadResultLabo();
            break;

          default:
            break;
        }
      }
      });
    loadAntTrait();
    loadIntervention();
    if (tabsConsultAnesth.activeLink.key == "reglement") {
      Reglement.reload(true);
    }
  });
</script>

<!-- Tab titles -->
<ul id="tab-consult-anesth" class="control_tabs">
  <li>
    <a id="acc_consultation_a_Atcd" href="#AntTrait" {{if $tabs_count.AntTrait == 0}}class="empty"{{/if}}>
      {{tr}}CAntecedent.more{{/tr}} <small>({{$tabs_count.AntTrait}})</small>
    </a>
  </li>
  <li onmousedown="refreshConstantesMedicales();">
    <a href="#constantes-medicales" {{if $tabs_count.Constantes == 0}}class="empty"{{/if}}>
      Constantes <small>({{$tabs_count.Constantes}})</small>
    </a>
  </li>
  <li onmousedown="this.onmousedown = ''; loadExams()">
    <a href="#Exams" {{if $tabs_count.Exams == 0}}class="empty"{{/if}}>
      Exam. Clinique <small>({{$tabs_count.Exams}})</small>
    </a>
  </li>
  <li>
    <a href="#Intub" {{if $tabs_count.Intub == 0}}class="empty"{{/if}}>
      Intubation <small>({{$tabs_count.Intub}})</small>
    </a>
  </li>
  <li>
    <a href="#ExamsComp" {{if $tabs_count.ExamsComp == 0}}class="empty"{{/if}}>
      Exam. Comp. <small>({{$tabs_count.ExamsComp}})</small>
    </a>
  </li>
  <li onmousedown="this.onmousedown = ''; loadInfosAnesth()">
    <a href="#InfoAnesth" {{if $tabs_count.InfoAnesth == 0}}class="empty"{{/if}}>
      Infos. Anesth. <small>({{$tabs_count.InfoAnesth}})</small>
    </a>
  </li>
  {{if $isPrescriptionInstalled && "dPcabinet CPrescription view_prescription"|conf:"CGroups-$g"}}
    <li onmousedown="this.onmousedown = ''; Prescription.reloadPrescSejour('', DossierMedical.sejour_id,'', '1', null, null, null,'', null, false);">
      <a href="#prescription_sejour" {{if $tabs_count.prescription_sejour == 0}}class="empty"{{/if}}>
        Prescription <small>({{$tabs_count.prescription_sejour}})</small>
      </a>
    </li>
  {{/if}}
  {{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
    <li onmousedown="refreshFacteursRisque();">
      <a href="#facteursRisque" {{if $tabs_count.facteursRisque == 0}}class="empty"{{/if}}>
        Facteurs de risque <small>({{$tabs_count.facteursRisque}})</small>
      </a>
    </li>
  {{/if}}
  {{if $app->user_prefs.ccam_consultation == 1}}
    <li onmousedown="this.onmousedown = ''; loadActes()">
      <a id="acc_consultation_a_Actes" href="#Actes" {{if $tabs_count.Actes == 0}}class="empty"{{/if}}>
        {{tr}}CCodable-actes{{/tr}} <small>({{$tabs_count.Actes}})</small>
      </a>
    </li>
  {{/if}}
  <li onmousedown="this.onmousedown = ''; loadDocs()">
    <a href="#fdrConsult" {{if $tabs_count.fdrConsult == 0}}class="empty"{{/if}}>
      Documents <small>({{$tabs_count.fdrConsult}})</small>
    </a>
  </li>
  <li onmousedown="Reglement.reload(true);">
    <a id="a_reglements_consult" href="#reglement" {{if $tabs_count.reglement == 0}}class="empty"{{/if}}>
      Réglements <small>({{$tabs_count.reglement}})</small>
    </a>
  </li>

  {{if "dPImeds"|module_active && (!$consult->_ref_consult_anesth->_ref_sejour || !$consult->_ref_consult_anesth->_ref_sejour->_id)}}
    <li>
      <a href="#labo">
        {{tr}}Labo{{/tr}}
      </a>
    </li>
  {{/if}}
</ul>

<!-- Tabs -->
<div id="AntTrait" style="display: none;"></div>

<div id="constantes-medicales" style="display: none;">
  <!-- We put a fake form for the ExamCompFrm form, before we insert the real one -->
  <form name="edit-constantes-medicales" action="?" method="post" onsubmit="return false">
    <input type="hidden" name="_last_poids" value="{{$consult->_ref_patient->_ref_constantes_medicales->poids}}" />
    <input type="hidden" name="_last__vst" value="{{$consult->_ref_patient->_ref_constantes_medicales->_vst}}" />
  </form>
</div>

<div id="Exams" style="display: none;"></div>

<div id="Intub" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/intubation}}
</div>
<div id="ExamsComp" style="display: none;">
  {{mb_include module=cabinet template=inc_consult_anesth/acc_examens_complementaire}}
</div>
<div id="InfoAnesth" style="display: none;"></div>

{{if $isPrescriptionInstalled && "dPcabinet CPrescription view_prescription"|conf:"CGroups-$g"}}
  <div id="prescription_sejour" style="display: none"></div>
{{/if}}

{{if $conf.dPcabinet.CConsultAnesth.show_facteurs_risque}}
  <div id="facteursRisque" style="display: none;"></div>
{{/if}}

{{if $app->user_prefs.ccam_consultation == 1}}
  <div id="Actes" style="display: none;"></div>
{{/if}}

<div id="fdrConsult" style="display: none;"></div>

<!-- Reglement -->
<script type="text/javascript">
  Reglement.consultation_id = '{{$consult->_id}}';
  Reglement.user_id = '{{$userSel->_id}}';
  Reglement.register(false);
</script>

{{if "dPImeds"|module_active && (!$consult->_ref_consult_anesth->_ref_sejour || !$consult->_ref_consult_anesth->_ref_sejour->_id)}}
  <div id="labo" style="display: none;"></div>
{{/if}}
