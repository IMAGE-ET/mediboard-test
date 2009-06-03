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
      var form = getForm("addAffectationsejour_" + selected_hospitalisation);
    }else{
      var form = getForm("addAffectationsejour");
    }
    form.lit_id.value = selected_lit;
    form.submit();
  }
}

Droppables.addLit = function(lit_id) {
  Droppables.add("lit-" + lit_id, { 
    onDrop:function(element){
      DragDropSejour(element.id, lit_id)
    }, 
    hoverclass:'litselected'
  });
}

function DragDropSejour(sejour_id, lit_id){
  $(sejour_id).style.display="none";
  if(sejour_id == 'sejour_bloque') {
    sejour_id = "sejour";
  }
  var oForm = getForm("addAffectation" + sejour_id);
  oForm.lit_id.value = lit_id;
  oForm.submit();
}

function submitAffectationSplit(form) {
  form._new_lit_id.value = selected_lit;
  if (!selected_lit) {
    alert("Veuillez sélectionner un nouveau lit et revalider la date");
    return;
  }
  
  if (form._date_split.value <= form.entree.value || 
      form._date_split.value >= form.sortie.value) {
    var msg = "La date de déplacement (" + form._date_split.value + ") doit être comprise entre";
    msg += "\n- la date d'entrée: " + form.entree.value; 
    msg += "\n- la date de sortie: " + form.sortie.value;
    alert(msg);
    return;
  }
  
  form.submit();
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
	
  var form = null;
  
  if (form = getForm("entreeAffectation" + affectation_id)) {
    Calendar.regField(form.entree, dates, {noView: true, icon: 'images/icons/planning.png'});
  }
	
  // Sortie affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.outerAffect.stop
  }

  if (form = getForm("sortieAffectation" + affectation_id)) {
    Calendar.regField(form.sortie, dates, {noView: true, icon: 'images/icons/planning.png'});
  }
  
  // Déplacement affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.currAffect.stop
  }

  if (form = getForm("splitAffectation" + affectation_id)) {
    Calendar.regField(form._date_split, dates, {noView: true, icon: 'images/icons/move.gif'});
  }
}

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

function showAlerte() {
  var url = new Url("dPhospi", "vw_etat_semaine");
  url.popup(500, 250, "Alerte");
}

function reloadService(oElement, mode) {
  if (oElement.checked) {
    var idService = oElement.value;
    var url = new Url("dPhospi", "httpreq_vw_aff_service");
    url.addParam("service_id", idService);
    url.addParam("mode", mode);
    url.requestUpdate("service" + idService);
  }
}

ObjectTooltip.modes.timeHospi = {
  module: "dPplanningOp",
  action: "httpreq_get_hospi_time",
  sClass: "tooltip",
}

ObjectTooltip.createTimeHospi = function (element, chir_id, codes) {
	ObjectTooltip.createEx(element, null, 'timeHospi', { 
		chir_id : chir_id, 
		codes : codes, 
		javascript : 0 
	} );
}