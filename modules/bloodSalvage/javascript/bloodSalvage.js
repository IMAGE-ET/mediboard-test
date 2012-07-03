/* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function submitStartTiming(oForm) {
  onSubmitFormAjax(oForm, function() {       
    reloadStartTiming(oForm.blood_salvage_id.value);
  });
}

function reloadStartTiming(blood_salvage_id){ 
  var url = new Url("bloodSalvage", "httpreq_vw_recuperation_start_timing");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("start-timing");
}

function reloadInfos(blood_salvage_id) {
  var url = new Url("bloodSalvage", "httpreq_vw_bloodSalvage_infos");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate('cell-saver-infos');
}

function submitFSEI(oForm) {
  if(oForm.type_ei_id.value) {
    onSubmitFormAjax(oForm, function() {
      doFiche(oForm.blood_salvage_id.value, oForm.type_ei_id.value);
    });
  } else {
    onSubmitFormAjax(oForm);
  }
}

function doFiche(blood_salvage_id,type_ei_id) {
  var url = new Url("dPqualite", "vw_incident");
  url.addParam("type_ei_id", type_ei_id);
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.popup(750, 500, "fsei");
}

function submitNurse(oForm){
  onSubmitFormAjax(oForm, function() {
    reloadNurse(getForm("affectNurse").object_id.value);
  });
}

function printRapport() {
  var url = new Url("bloodSalvage", "print_rapport"); 
  url.addElement(document.rapport.blood_salvage_id);
  url.popup(700, 500, "printRapport");
}

function submitBloodSalvageTiming(oForm) {
  onSubmitFormAjax(oForm, function() { 
    reloadBloodSalvageTiming(oForm.blood_salvage_id.value);
  });
}
 
function reloadTotalTime(blood_salvage_id) {
  var url = new Url("bloodSalvage", "httpreq_total_time");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("totaltime");
}

function reloadBloodSalvageTiming(blood_salvage_id){ 
  var url = new Url("bloodSalvage", "httpreq_vw_bs_sspi_timing");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("timing");
  reloadTotalTime(blood_salvage_id);  
}

function reloadNurse(blood_salvage_id){
  var url = new Url("bloodSalvage", "httpreq_vw_blood_salvage_personnel");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("listNurse");
}

submitNewBloodSalvage = function(oForm) {
  onSubmitFormAjax(oForm, function() {
    var url = new Url("bloodSalvage", "httpreq_vw_bloodSalvage");
    url.requestUpdate("bloodsalvage_form");
  });
}

viewRSPO = function(operation_id) {
  var url = new Url("bloodSalvage","httpreq_vw_sspi_bs");
  url.addParam("op",operation_id);
  url.popup(800,600,"bloodSalvage_sspi");
}
