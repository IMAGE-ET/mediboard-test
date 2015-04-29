
updateTokenCim10 = function() {
  onSubmitFormAjax(getForm("editDiagFrm"), DossierMedical.reloadDossierPatient);
};

updateTokenCim10Anesth = function(){
  onSubmitFormAjax(getForm("editDiagAnesthFrm"), DossierMedical.reloadDossierSejour);
};

onSubmitAnt = function (form, type_see) {
  var rques = $(form.rques);
  if (!rques.present()) {
    return false;
  }

  onSubmitFormAjax(form, {onComplete : function() {
    if (type_see) {
      DossierMedical.reloadDossierPatient(null, type_see);
    }
    else {
      DossierMedical.reloadDossiersMedicaux();
    }
    if (window.reloadAtcd) {
      reloadAtcd();
    }
  }});

  // Après l'ajout d'antécédents
  if (Preferences.empty_form_atcd == "1") {
    $V(form.date    , "");
    $V(form.date_da , "");
    $V(form.type    , "");
    $V(form.appareil, "");
  }

  $V(form.keywords_composant, "");
  $V(form.cds, "");

  // Dans le cas des CDS, on vide le type
  if ($V(form._idex_tag) == "COMPENDIUM_CDS") {
    $V(form.type    , "");
  }

  $V(form._idex_code, "");
  $V(form._idex_tag, "");

  rques.clear().focus();

  return false;
};

DossierMedical = {
  sejour_id: null,
  patient_id: null,
  dossier_anesth_id: null,
  _is_anesth: null,
  reload_dbl: false,
  sort_by_date : Preferences.sort_atc_by_date,

  updateSejourId : function(sejour_id) {
    this.sejour_id = sejour_id;

    // Mise à jour des formulaire
    if(document.editTrmtFrm){
      $V(document.editTrmtFrm._sejour_id, sejour_id, false);
    }
    if(document.editAntFrm){
      $V(document.editAntFrm._sejour_id, sejour_id, false);
    }
  },

  reloadDossierPatient: function(id_div, type_see) {
    if (type_see == 'traitement' && !id_div) {
      id_div = 'list_traitements';
    }
    id_div = id_div ? id_div : 'listAnt';
    type_see = type_see ? type_see : '';
    var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents");
    antUrl.addParam("patient_id"  , DossierMedical.patient_id);
    antUrl.addParam("_is_anesth"  , DossierMedical._is_anesth);
    antUrl.addParam("sort_by_date", DossierMedical.sort_by_date);
    antUrl.addParam("dossier_anesth_id", DossierMedical.dossier_anesth_id);
    antUrl.addParam("type_see", type_see);
    if (DossierMedical._is_anesth) {
      antUrl.addParam("sejour_id", DossierMedical.sejour_id);
    }
    if (DossierMedical.reload_dbl) {
      refreshAntecedentsPatient();
    }
    antUrl.requestUpdate(id_div);
  },

  toggleSortAntecedent: function (type_see) {
    if (DossierMedical.sort_by_date == 1) {
      DossierMedical.sort_by_date = 0;
    }
    else {
      DossierMedical.sort_by_date = 1;
    }
    DossierMedical.reloadDossierPatient(null, type_see);


  },
  reloadDossierSejour: function(){
    if ($('listAntCAnesth')) {
      var antUrl = new Url("dPcabinet", "httpreq_vw_list_antecedents_anesth");
      antUrl.addParam("sejour_id", DossierMedical.sejour_id);
      antUrl.requestUpdate('listAntCAnesth');
    }
  },

  reloadDossiersMedicaux: function(){
    DossierMedical.reloadDossierPatient();
    if (DossierMedical._is_anesth || DossierMedical.sejour_id) {
      DossierMedical.reloadDossierSejour();
    }
  }
};