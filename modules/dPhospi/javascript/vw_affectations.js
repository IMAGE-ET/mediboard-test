function flipChambre(chambre_id) {
  Element.classNames("chambre-" + chambre_id).flip("chambrecollapse", "chambreexpand");
}

function flipSejour(sejour_id) {
  Element.classNames("sejour_" + sejour_id).flip("sejourcollapse", "sejourexpand");
}

var selected_hospitalisation = null;
var selected_hospi = false;
function selectHospitalisation(sejour_id) {
  var element = document.getElementById("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }
  selected_hospitalisation = sejour_id;
  selected_hospi = true;
  submitAffectation();
}

var selected_lit = null;

function selectLit(lit_id) {
  var element = document.getElementById("lit" + selected_lit);
  if (element) {
    element.checked = false;
  }
  selected_lit = lit_id;
  submitAffectation();
}

function submitAffectation() {
  if (selected_lit && selected_hospi) {
    if(selected_hospitalisation){
      var form = eval("document.addAffectationsejour_" + selected_hospitalisation);
    }else{
      var form = eval("document.addAffectationsejour");    
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
  var oForm = eval("document.addAffectation" + sejour_id);
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

Calendar.setupAffectation = function(affectation_id, affectOptions) {
  var options = {
    sejour : {
      start : null,
      stop : null
    },
    
    currAffect : {
      start : null,
      stop : null
    },
    
    outerAffect : {
      start : null,
      stop : null
    }
  }
  
  
  Object.extend(options, affectOptions);

  var dates = {
    current: options.sejour,
	  spots: []
  }
  
  // Entrée affectation
	dates.limit = {
    start: options.outerAffect.start,
    stop: options.currAffect.stop
  }

	Calendar.prepareDates(dates);
	
  var form = null;
  
  if (form = eval("document.entreeAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_entree",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_entree",
        showsTime   : true,
        firstDay    : 1,
        dateStatusFunc: Calendar.dateStatus.bind(Object.clone(dates)),
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            var form = eval("document.entreeAffectation" + affectation_id);
            form.submit();
          }
        }
        
      }
    );
  }
	
  // Sortie affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.outerAffect.stop
  }
	
	Calendar.prepareDates(dates);

  if (form = eval("document.sortieAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_sortie",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_sortie",
        showsTime   : true,
        firstDay    : 1,
        dateStatusFunc: Calendar.dateStatus.bind(Object.clone(dates)),
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            var form = eval("document.sortieAffectation" + affectation_id);
            form.submit();
          }
        }
      }
    );
  }
  
  // Déplacement affectation
	dates.limit = {
    start: options.currAffect.start,
    stop: options.currAffect.stop
  }

	Calendar.prepareDates(dates);

  if (form = eval("document.splitAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "__date_split",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_split",
        showsTime   : true,
        firstDay    : 1,
        dateStatusFunc: Calendar.dateStatus.bind(dates),
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            var form = eval("document.splitAffectation" + affectation_id);
            submitAffectationSplit(form);
          }
        }
      }
    );
  }
}

function popPlanning() {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
}

function showRapport(date) {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_rapport");
  url.addParam("date", date);
  url.popup(800, 600, "Rapport");
}

function showLegend() {
  var url = new Url;
  url.setModuleAction("dPhospi", "legende");
  url.popup(500, 500, "Legend");
}

function showAlerte() {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_etat_semaine");
  url.popup(500, 250, "Alerte");
}

function reloadService(oElement, mode) {
  if (oElement.checked) {
    var idService = oElement.value;
    var url = new Url;
    url.setModuleAction("dPhospi", "httpreq_vw_aff_service");
    url.addParam("service_id", idService);
    url.addParam("mode", mode);
    url.requestUpdate("service" + idService);
  }
}

var PrevTimeHospi = {
  show: function(affectation_id, chir_id, codes) {
	  var oElement = $("tpsPrev"+affectation_id);
	  oElement.show();
	  if(oElement.alt != "infos - cliquez pour fermer") {
	    var url = new Url;
	    url.setModuleAction("dPplanningOp", "httpreq_get_hospi_time");
	    url.addParam("chir_id", chir_id);
	    url.addParam("codes", codes);
	    url.addParam("javascript", 0);
	    url.requestUpdate(oElement);
	    oElement.alt = "infos - cliquez pour fermer";
	  }
	},
	
	hide: function(affectation_id) {
	  var oElement = $("tpsPrev"+affectation_id);
	  oElement.hide();
	}  
}