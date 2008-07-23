
function submitStartTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() {       
      reloadStartTiming(oForm.blood_salvage_id.value);
    } 
  });
}

function reloadStartTiming(blood_salvage_id){ 
    var url = new Url();
    url.setModuleAction("bloodSalvage", "httpreq_vw_recuperation_start_timing");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate("start-timing", { waitingText: null } );
}

function reloadInfos(blood_salvage_id) {
  var url = new Url(); 
    url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage_infos");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate('cell-saver-infos', { waitingText: null } );
}

function submitFSEI(oForm) {
  if(oForm.type_ei_id.value) {
    submitFormAjax(
      oForm,'systemMsg', { 
      onComplete : function() {
        doFiche(oForm.blood_salvage_id.value, oForm.type_ei_id.value);
        }
      }
    );
  } else {
    submitFormAjax(oForm,'systemMsg');
  }
}

function doFiche(blood_salvage_id,type_ei_id) {
  var url = new Url;
  url.setModuleAction("dPqualite","vw_incident");
  url.addParam("type_ei_id",type_ei_id);
  url.addParam("blood_salvage_id",blood_salvage_id);
  url.popup(750,500,"fsei");
  return;
}

function submitNurse(oForm){
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() {
      reloadNurse(document.forms["affectNurse"].object_id.value)
    } 
  });
}

function printRapport() {
  var url = new Url;
  url.setModuleAction("bloodSalvage", "print_rapport"); 
  url.addElement(document.rapport.blood_salvage_id);
  url.popup(700, 500, "printRapport");
  return;
}

function submitTiming(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() { 
      reloadTiming(oForm.blood_salvage_id.value);
    } 
  });
}

function submitInfos(oForm) {
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function() { 
      reloadInfos(oForm.blood_salvage_id.value);
    } 
  });
}

function reloadInfos(blood_salvage_id) {
  var url = new Url(); 
    url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage_volumes");
    url.addParam("blood_salvage_id", blood_salvage_id);
    url.requestUpdate('cell-saver-infos', { waitingText: null } );
}

function reloadTotalTime(blood_salvage_id) {
  var url = new Url();
  url.setModuleAction("bloodSalvage", "httpreq_total_time");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("totaltime", { waitingText: null } );
}

function reloadTiming(blood_salvage_id){ 
  var url = new Url();
  url.setModuleAction("bloodSalvage", "httpreq_vw_bs_sspi_timing");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("timing", { waitingText: null } );
  reloadTotalTime(blood_salvage_id);  
}

function reloadNurse(blood_salvage_id){
  var url = new Url;
  url.setModuleAction("bloodSalvage", "httpreq_vw_blood_salvage_personnel");
  url.addParam("blood_salvage_id", blood_salvage_id);
  url.requestUpdate("listNurse", {
    waitingText: null
  } );
}

submitNewBloodSalvage = function(oForm) {
	submitFormAjax(oForm,'systemMsg',{ onComplete: function() {
	    var url = new Url;
	    url.setModuleAction("bloodSalvage", "httpreq_vw_bloodSalvage");
	    url.requestUpdate("bloodSalvage");
	}
	});
}

viewRSPO = function(operation_id) {
  var url = new Url;
  url.setModuleAction("bloodSalvage","httpreq_vw_sspi_bs");
  url.addParam("op",operation_id);
  url.popup(800,600,"bloodSalvage_sspi");
}

