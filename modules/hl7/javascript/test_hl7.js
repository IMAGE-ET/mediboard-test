/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

TestHL7 = {
  refreshListDemographicSupplier : function (form) {
    return Url.update(form, "list_demographic");
  },

  changePageListDemographicSupplier : function (start) {
    var form = getForm("filter-pat-demographic-supplier");
    $V(form.page, start);
    return TestHL7.refreshListDemographicSupplier(form);
  },

  refreshListEncounterSupplier : function (form) {
    return Url.update(form, "list_encounter");
  },

  changePageListEncounterSupplier : function (start) {
    var form = getForm("filter-pat-encounter-supplier");
    $V(form.page, start);
    return TestHL7.refreshListEncounterSupplier(form);
  },

  showPatientGenerator : function () {
    var url = new Url("hl7","ajax_generator");
    url.requestUpdate("generate");
  },

  random : function (field, object_class) {
    new Url("hl7", "ajax_random_field")
      .addParam("field", field)
      .addParam("class", object_class)
      .requestJSON(function(data) {
        var form = getForm('generatePatient');
        $V(form[data.field], data.value);
      });
  },

  randomAll : function () {
    getForm('generatePatient')
      .select("input[type=text], textarea, select")
      .each(function(item) {
        TestHL7.random(item.name, "CPatient");
      });
  },

  clear : function () {
    getForm('generatePatient')
      .select("input[type=text], textarea, select")
      .each(function(item) {
        item.value = "";
      });
  },

  sendA28 : function(patient_id) {
    new Url("hl7", "ajax_send_hl7v2_iti30_event")
      .addParam("patient_id" , patient_id)
      .addParam("event"      , "A28")
      .requestUpdate("systemMsg");
  },

  sendA31 : function(patient_id) {
    new Url("dPpatients", "vw_edit_patients").
      addParam("patient_id", patient_id).
      addParam("dialog"    , 1).
      requestModal();
  },

  handleMergeClick: function(form) {
    var checked = $A(form.elements.merge_patient_id).filter(function(c){
      return c.checked;
    });

    if (checked.length == 2) {
      TestHL7.sendA40($V(form.receiver_id), checked[0].value, checked[1].value);
      checked.each(function(c){
        c.checked = false;
      });
    }
  },

  sendA40 : function(receiver_id, patient1_id, patient2_id) {
    window.onMergeComplete = function(){
      getForm("filter-pat-demographic-supplier").onsubmit();
    };

    var url = new Url("system", "object_merger").
      addParam("objects_class", "CPatient").
      addParam("objects_id", [patient1_id, patient2_id].join('-'));
    url.pop(900, 800);
  },

  selectPatient : function(patient_id) {
    new Url("hl7", "ajax_encounter_action")
      .addParam("patient_id", patient_id)
      .requestUpdate("search_encounter");
  },

  searchPatient : function() {
    new Url("hl7", "ajax_encounter_search_patient")
      .requestUpdate("search_encounter");
  },

  sendTest : function(form) {
    new Url("hl7", "ajax_encounter_event")
      .addFormData(form)
      .requestModal();

    return false;
  },

  findValueSet : function(form) {
    if (!checkForm(form)) {
      return false;
    }

    new Url("hl7", "ajax_find_value_set")
      .addFormData(form)
      .requestUpdate("search_value_set");

    return false;
  }
};