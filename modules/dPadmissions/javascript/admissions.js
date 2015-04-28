Admissions = {
  totalUpdater: null,
  listUpdater:  null,
  target_date: null,
  pre_admission_filter: null,
  table_id: null,

  filter: function(input, table) {
    table = $(table);
    table.select("tr").invoke("show");
    
    var term = $V(input);
    if (!term) return;
    
    table.select(".CPatient-view").each(function(e) {
      if (!e.innerHTML.like(term)) {
        e.up("tr").hide();
      }
    });
  },
  
  togglePrint: function(status) {
    var table = $(Admissions.table_id);
    table.select("input[name=print_doc]").each(function(elt) {
      elt.checked = status ? "checked" : "";
    });
  },
  
  printDHE: function(type, object_id) {
    var url = new Url("planningOp", "view_planning");
    url.addParam(type, object_id);
    url.popup(700, 550, "DHE");
  },

  choosePrintForSelection: function() {
    Admissions.beforePrint();
    Modal.open('area_prompt_modele', {width: '500px', height: '300px'});
  },

  printForSelection: function(modele_id) {
    if (!modele_id) {
      alert("Veuillez choisir un modèle avant de lancer l'impression");
      return false;
    }
    var table = $(Admissions.table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
    
    if (sejours_ids == "") {
      alert("Veuillez sélectionner au minimum un patient pour l'impression");
      return false;
    }
    
    var oForm = getForm("chooseDoc");
    $V(oForm.sejours_ids, sejours_ids.join(","));
    oForm.submit();
    return true;
  },
  
  rememberSelection: function() {
    var table = $(Admissions.table_id);
    window.sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
  },
  
  restoreSelection: function() {
    var table = $(Admissions.table_id);
    
    table.select("input[name=print_doc]").each(function(elt) {
      if ($H(window.sejours_ids).index(elt.value)) {
        elt.checked = true;
      }
    });
  },

  printFichesAnesth: function() {
    var url = new Url("admissions", "print_fiches_anesth");
    var table = $(Admissions.table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");
    
    if (sejours_ids == "") {
      alert("Veuillez sélectionner au minimum un patient pour l'impression");
      return false;
    }
    
    url.addParam("sejours_ids", sejours_ids.join(","));
    url.addParam("pdf", 0);
    url.popup(700, 500);
  },

  printPlanSoins: function() {
    var url = new Url("soins", "offline_plan_soins");
    var table = $(Admissions.table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");

    if (sejours_ids == "") {
      alert("Veuillez sélectionner au minimum un patient pour l'impression");
      return false;
    }

    url.addParam("sejours_ids", sejours_ids.join(","));
    url.addParam("mode_dupa", 1);
    url.popup(700, 500);
  },

  chooseEtiquette: function() {
    var url = new Url("hospi", "ajax_choose_modele_etiquette");
    url.addParam("object_class", "CSejour");
    url.addParam("custom_function", "Admissions.printEtiquettes");
    url.requestModal("40%", "40%");
  },

  printEtiquettes: function(object_class, object_id, modele_etiquette_id) {
    var table = $(Admissions.table_id);
    var sejours_ids = table.select("input[name=print_doc]:checked").pluck("value");

    if (sejours_ids == "") {
      alert("Veuillez sélectionner au minimum un patient pour l'impression");
      return false;
    }

    var form = getForm("download_etiqs");
    $V(form.modele_etiquette_id, modele_etiquette_id);
    $V(form.sejours_ids, sejours_ids.join("-"));
    form.submit();
    Control.Modal.close();
  },

  beforePrint: function() {
    Admissions.totalUpdater.stop();
    Admissions.listUpdater.stop();
  },

  afterPrint: function() {
    Control.Modal.close();
    Admissions.totalUpdater.resume();
    Admissions.listUpdater.resume();
  },
  
  toggleMultipleServices: function(elt) {
    var status = elt.checked;
    var form = elt.form;
    var elt_service_id = form.service_id;
    elt_service_id.multiple = status;
    elt_service_id.size = status ? 5 : 1;
  },
  
  showLegend: function() {
    new Url("admissions", "vw_legende").requestModal();
  },

  showDocs: function(sejour_id) {
    Admissions.totalUpdater.stop();
    Admissions.listUpdater.stop();
    var url = new Url("hospi", "httpreq_documents_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("only_sejour", 1);
    url.addParam("with_patient", 1);
    url.requestModal(700, 400);
    url.modalObject.observe("afterClose", function() {
      Admissions.totalUpdater.resume();
      Admissions.listUpdater.resume();
    });
  },


  updateSummaryPreAdmissions : function (sdate) {
    if (sdate) {this.target_date = sdate;}
    var admUrl = new Url("admissions", "httpreq_vw_all_preadmissions");
    admUrl.addParam("date", this.target_date);
    admUrl.requestUpdate('allPreAdmissions');
  },

  updatePeriodicalSummaryPreAdmissions : function() {
    setInterval(function(){
      Admissions.updateSummaryPreAdmissions();
    }, 120000);
  },

  updateListPreAdmissions : function (sdate) {
    var admUrl = new Url("admissions", "httpreq_vw_preadmissions");
    if (sdate) {
      this.target_date = sdate;
      admUrl.addParam("date", this.target_date);
    }
    admUrl.addParam("filter", this.pre_admission_filter);
    admUrl.requestUpdate('listPreAdmissions');

    //update du selecteur
    var lines = $("allPreAdmissions").select('table tbody tr.preAdmission-day').invoke("removeClassName", "selected");
    var target_td = $('paday_'+this.target_date);
    if (target_td) {
      target_td.addClassName("selected");
    }
  },

  updatePeriodicalPreAdmissions : function() {
    setInterval(function() {
      Admissions.updateListPreAdmissions();
    }, 120000);
  },

  validerEntree : function(sejour_id, callback, callback_close) {
    new Url("admissions", "ajax_edit_entree")
      .addParam("sejour_id"           , sejour_id)
      .addParam("module"              , App.m)
      .requestModal("725px", "335px")
      .modalObject.observe("afterClose", callback_close);
    document.stopObserving("mb:valider_entree");
    document.observe("mb:valider_entree", callback);
  },

  validerSortie : function (sejour_id, modify_sortie_prevue, callback, callback_close) {
    new Url("admissions", "ajax_edit_sortie")
      .addParam("sejour_id"           , sejour_id)
      .addParam("module"              , App.m)
      .addParam("modify_sortie_prevue", modify_sortie_prevue ? 1 : 0)
      .requestModal("725px", "335px")
      .modalObject.observe("afterClose", callback_close);
    document.stopObserving("mb:valider_sortie");
    document.observe("mb:valider_sortie", callback);
  },

  changeSortie : function (form, sejour_id) {
    var mode_sortie = $V(form.mode_sortie);

    //Affichage des champs complémentaires en fonction du mode de sortie
    $('sortie_transfert_'+sejour_id).setVisible(mode_sortie == "transfert");
    $('sortie_service_mutation_'+sejour_id).setVisible(mode_sortie == "mutation");
    $('lit_sortie_mutation_'+sejour_id).setVisible(mode_sortie === "mutation");
    $('sortie_deces_'+sejour_id).setVisible(mode_sortie === "deces");
    if ($('transport_sortie_mutation_'+sejour_id)) {
      $('transport_sortie_mutation_'+sejour_id).setVisible(mode_sortie != "mutation");
    }
    //Suppression des valeurs lors du changement de mode de sortie
    if (mode_sortie != "mutation") {
      $V(form.service_sortie_id                        , "");
      $V(form.service_sortie_id_autocomplete_view      , "");
    }

    if (mode_sortie != "transfert") {
      $V(form.etablissement_sortie_id                  , "");
      $V(form.etablissement_sortie_id_autocomplete_view, "");
    }

    if (mode_sortie != "deces") {
      $V(form._date_deces                              , "");
      $V(form._date_deces_da                           , "");
    }

    var label_deces = form._date_deces.getLabel();

    label_deces.removeClassName("notNull");
    form._date_deces.removeClassName("notNull");

    if (mode_sortie === "deces") {
      label_deces.addClassName("notNull");
      form._date_deces.addClassName("notNull");
      if (!$V(form._date_deces)) {
        $V(form._date_deces, $V(form.sortie_reelle));
        $V(form._date_deces_da, $V(form.sortie_reelle_da));
      }
    }
  },

  annulerSortie : function (form, callback) {
    if (!confirm("Merci de confirmer l\'annulation de sortie.")) {
      return false;
    }

    $V(form._sejours_enfants_ids   , "");
    $V(form.sortie_reelle          , "");
    $V(form.mode_sortie            , "normal");
    $V(form.mode_sortie_id         , "");
    form.mode_sortie.removeClassName("notNull");

    return onSubmitFormAjax(form, callback);
  },

  updateLitMutation : function (form) {
    var sejour_id = $V(form.sejour_id);
    new Url('dPadmissions', 'ajax_refresh_lit')
      .addParam('sejour_id'    , sejour_id)
      .addParam('sortie_reelle', $V(form.sortie_reelle))
      .requestUpdate("lit_sortie_mutation_"+sejour_id);
  },

  choisirLit : function (element) {
    if (element.selectedIndex >=0) {
      var option = element.options[element.selectedIndex];
      $V(element.form.service_sortie_id, option.get("service_id"));
      $V(element.form.service_sortie_id_autocomplete_view, option.get("name"));
    }
  },

  askconfirm : function (sejour_id) {
    Modal.open("confirmSortieModal_"+sejour_id, {
      width: "410px",
      height: "300px"
    });
  },

  afterConfirmPassword : function (sejour_id, id_user) {
    var form_sortie = getForm("validerSortie"+sejour_id);
    var form_confirm = getForm("confirmSortie_"+sejour_id);
    //cas de la confirmation de l'autorisation de sortie
    var user_id = id_user || $V(form_confirm.user_id);
    if ($V(form_sortie.action_confirm) == 1) {
      $V(form_sortie.confirme_user_id, user_id);
      if (!$V(form_sortie.confirme)) {
        var sortie_prevue = $V(form_sortie.sortie_prevue);
        var sortie_reelle = $V(form_sortie.sortie_reelle);
        var sortie = sortie_reelle ? sortie_reelle : sortie_prevue;
        $V(form_sortie.confirme, sortie);
      }
    }
    //cas de l'annulation de l'autorisation de sortie
    else {
      $V(form_sortie.confirme, "");
      $V(form_sortie.confirme_user_id, "");
    }

    form_sortie.onsubmit()
  },

  confirmationSortie : function (form, modify_sortie_prevue, sortie_prevue, impose_lit_service_mutation, callback) {
    if (impose_lit_service_mutation && App.m == "dPurgences") {
      if ((!form.lit_id || !form.lit_id.value) && !form.service_sortie_id.value) {
        alert($T('CRPU-_missing_lit_service_mutation'));
        return false;
      }
    }

    if (!modify_sortie_prevue && !$V(form.entree_reelle)) {
      if (!confirm('Attention, ce patient ne possède pas de date d\'entrée réelle, souhaitez vous confirmer la sortie du patient ?')) {
        return false;
      }
    }

    var sortie_relle = $V(form.sortie_reelle);

    if (sortie_relle) {
      sortie_relle     = Date.fromDATETIME(sortie_relle);
      sortie_prevue    = Date.fromDATETIME(sortie_prevue);
      if (App.m !== "dPurgences" && sortie_relle.getDate() != sortie_prevue.getDate() || sortie_relle.getFullYear() != sortie_prevue.getFullYear()) {
        if (!confirm('La date de sortie enregistrée est différente de la date prévue, souhaitez vous confirmer la sortie du patient ?')) {
          return false;
        }
      }
    }

    if ($V(form.mode_sortie) === "deces") {
      if (!confirm('Confirmez-vous le décès de '+$V(form.view_patient)+' le '+$V(form._date_deces_da)+' ?')) {
        return false;
      }
      $V(form.sortie_reelle   , $V(form._date_deces));
      $V(form.sortie_reelle_da, $V(form._date_deces_da));
    }

    if (form._sejours_enfants_ids) {
      var tokenfield = new TokenField(form._sejours_enfants_ids);
      tokenfield.getValues().each( (function(form, modify_sortie_prevue, element) {
        var form_enfant = getForm("validerSortieEnfant"+element);
        if (!form_enfant) {
          return;
        }
        //Si nous sommes en autorisation de sortie et que l'enfant a déjà l'autorisation, on abandonne le traitement
        if (modify_sortie_prevue && $V(form_enfant.confirme)) {
          return;
        }
        //si nous sommes en validation de sortie et que l'enfant est déjà sortie, on abandonne le traitement
        if (!modify_sortie_prevue && $V(form_enfant.sortie_reelle)) {
          return;
        }
        var text = "Voulez-vous effectuer dans un même temps la sortie de l'enfant ";
        if (modify_sortie_prevue) {
          text = "Voulez-vous autoriser dans un même temps la sortie de l'enfant ";
        }
        if (confirm(text+$V(form_enfant.view_patient))) {
          if (form.mode_sortie_id) {
            $V(form_enfant.mode_sortie_id, $V(form.mode_sortie_id));
          }

          if (modify_sortie_prevue) {
            $V(form_enfant.sortie_prevue   , $V(form.sortie_prevue));
            $V(form_enfant.confirme        , $V(form.confirme));
            $V(form_enfant.confirme_user_id, $V(form.confirme_user_id));
          }
          else {
            $V(form_enfant.sortie_reelle, $V(form.sortie_reelle));
          }

          $V(form_enfant.mode_sortie  , $V(form.mode_sortie));
          form_enfant.onsubmit()
        }
      }).curry(form, modify_sortie_prevue));
    }

    return onSubmitFormAjax(form, callback);
  },

  //Changement de la destination en fonction du mode sortie
  changeDestination : function(form) {
    //Contrainte à appliquer pour la destination
    var contrainteDestination = {
      "mutation"  : ["", 1, 2, 3, 4],
      "transfert" : ["", 1, 2, 3, 4],
      "normal"    : ["", 0, 6, 7],
      "deces"     : ["", 0]
    };

    var destination = form.elements.destination;
    var mode_sortie = $V(form.elements.mode_sortie);

    // Aucun champ trouvé
    if (!destination) {
      return true;
    }

    //Pas de mode de sortie, activation de tous les options
    if (!mode_sortie) {
      $A(destination).each(function (option) {
        option.disabled = false
      });
      return true;
    }

    //Application des contraintes
    $A(destination).each(function (option) {
      option.disabled = !contrainteDestination[mode_sortie].include(option.value);
    });

    if (destination[destination.selectedIndex].disabled) {
      $V(destination, "");
    }

    if (!$V(destination) && destination.hasClassName("notNull") && (mode_sortie == "deces" || mode_sortie == "normal")) {
      $V(destination, "0");
    }

    return true;
  },

  selectServices: function(view) {
    var url = new Url("hospi", "ajax_select_services");
    url.addParam("view", view);
    url.addParam("ajax_request", 0);
    url.requestModal(null, null, {maxHeight: '600'});
  }
};
