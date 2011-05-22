function flipChambre(chambre_id) {
  Element.classNames("chambre-" + chambre_id).flip("chambrecollapse", "chambreexpand");
}

function flipSejour(sejour_id) {
  Element.classNames("sejour_" + sejour_id).flip("sejourcollapse", "sejourexpand");
}

var selected_hospitalisation = null;
var selected_hospi = false;
function selectHospitalisation(sejour_id) {
  var element = $("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }
  selected_hospitalisation = sejour_id;
  selected_hospi = true;
  submitAffectation();
}

var selected_lit = null;
function selectLit(lit_id) {
  var element = $("lit" + selected_lit);
  if (element) {
    element.checked = false;
  }
  selected_lit = lit_id;
  submitAffectation();
}

function submitAffectation() {
  if (selected_lit && selected_hospi) {
    if(selected_hospitalisation){
      var oForm = getForm("addAffectationsejour_" + selected_hospitalisation);
    }else{
      var oForm = getForm("addAffectationsejour");
    }
    oForm.lit_id.value = selected_lit;
    
    return onSubmitFormAjax(oForm, {onComplete: reloadTableau});
  }
}

Droppables.addLit = function(lit_id) {
  Droppables.add("lit-" + lit_id, { 
    onDrop:function(element){
      DragDropSejour(element.id, lit_id);
    }, 
    hoverclass:'dropover'
  });
};

function DragDropSejour(sejour_id, lit_id){
  $(sejour_id).style.display="none";
  if(sejour_id == 'sejour_bloque') {
    sejour_id = "sejour";
  }
  var oForm = getForm("addAffectation" + sejour_id);
  oForm.lit_id.value = lit_id;
  return onSubmitFormAjax(oForm, {onComplete: reloadTableau});
}

function submitAffectationSplit(oForm) {
  oForm._new_lit_id.value = selected_lit;
  if (!selected_lit) {
    alert("Veuillez sélectionner un nouveau lit et revalider la date");
    return;
  }
  
  if (oForm._date_split.value <= oForm.entree.value || 
      oForm._date_split.value >= oForm.sortie.value) {
    var msg = "La date de déplacement (" + oForm._date_split.value + ") doit être comprise entre";
    msg += "\n- la date d'entrée: " + oForm.entree.value;
    msg += "\n- la date de sortie: " + oForm.sortie.value;
    alert(msg);
    return;
  }
  return onSubmitFormAjax(oForm, {onComplete: reloadTableau});
}

Calendar.setupAffectation = function(affectation_id, options) {
  options = Object.extend({
    currAffect : {
      start : null,
      stop : null
    },
    outerAffect : {
      start : null,
      stop : null
    }
  }, options);

  var dates = {
    limit: {// Entrée affectation
      start: options.outerAffect.start,
      stop: options.currAffect.stop
    }
  };
	
  var form;
  
  if (form = getForm("entreeAffectation" + affectation_id)) {
    Calendar.regField(form.entree, dates, {noView: true, icon: 'images/icons/calendar.gif'});
  }
	
  // Sortie affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.outerAffect.stop
  };

  if (form = getForm("sortieAffectation" + affectation_id)) {
    Calendar.regField(form.sortie, dates, {noView: true, icon: 'images/icons/calendar.gif'});
  }
  
  // Déplacement affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.currAffect.stop
  };

  if (form = getForm("splitAffectation" + affectation_id)) {
    Calendar.regField(form._date_split, dates, {noView: true, icon: 'images/icons/move.gif'});
  }
};

function popPlanning() {
  var url = new Url("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
}

function showRapport(date) {
  var url = new Url("dPhospi", "vw_rapport");
  url.addParam("date", date);
  url.popup(800, 600, "Rapport");
}

function showLegend() {
  var url = new Url("dPhospi", "legende");
  url.popup(500, 500, "Legend");
}

function showAlerte(sType_admission) {
  var url = new Url("dPhospi", "vw_etat_semaine");
  url.addParam("type_admission", sType_admission);
  url.popup(500, 250, "Alerte");
}

function toggleService(trigger, mode) {
  var cookie = new CookieJar(),
      service_id = trigger.value,
      container_id = "service" + service_id;
      
  if (trigger.checked) {
    var url = new Url("dPhospi", "httpreq_vw_aff_service");
    url.addParam("service_id", service_id);
    url.addParam("mode", mode);
    url.requestUpdate(container_id);
  }
  
  $(container_id).setVisible(trigger.checked);
  cookie.setValue("fullService", container_id, trigger.checked);
}

function initServicesState(){
  var servicesState = new CookieJar().get("fullService"),
      form = getForm("chgAff");
  $H(servicesState).each(function(p){
    var visible = (p.value !== false);
    form.elements[p.key].checked = visible;
    $(p.key).setVisible(visible);
  });
}

ObjectTooltip.modes.timeHospi = {
  module: "dPplanningOp",
  action: "httpreq_get_hospi_time",
  sClass: "tooltip"
};

ObjectTooltip.createTimeHospi = function (element, chir_id, codes) {
	ObjectTooltip.createEx(element, null, 'timeHospi', { 
		chir_id : chir_id, 
		codes : codes, 
		javascript : 0 
	} );
};

function printTableau() {
  var oForm = getForm("chgAff");
  var url = new Url;
  url.setModuleAction("dPhospi", "print_tableau");
  url.addParam("date", $V(oForm.date));
  url.addParam("mode", $V(oForm.mode));
  url.addParam("services[]", $V(oForm["list_services[]"]), true);
  url.popup(850, 600, "printAffService");
}

function reloadTableau() {
  $("hospitalisation").checked = false;
  if(selected_hospi && selected_hospitalisation) {
    $("hospitalisation" + selected_hospitalisation).checked = false;
  }
  if(selected_hospi && selected_lit && selected_hospitalisation) {
    $("sejour_"+selected_hospitalisation).remove();
    selected_hospitalisation = null;
    selected_hospi = false;
  }
  selected_lit = null;
  
  var oForm  = getForm("chgAff");
  url = new Url;
  url.addElement(oForm.date);
  url.addParam("list_services[]", $V(oForm["list_services[]"]), true);
  url.addElement(oForm.mode)
  url.setModuleAction("dPhospi", "ajax_tableau_affectations_lits");
  url.requestUpdate("tableauAffectations");
}
