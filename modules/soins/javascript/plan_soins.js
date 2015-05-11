PlanSoins = {
  composition_dossier: null,
  timeOutBefore:       null,
  timeOutAfter:        null,
  date:                null,
  manual_planif:       null,
  bornes_composition_dossier: null,
  nb_postes:           null,
  anciennete:          null,
  time_anciennete:     [],
  timer_anciennete:    [],
  nb_decalage:         null,
  save_nb_decalage:    null,
  plan_soin_id:        null,// L'id de l'element plan de soin
  regroup_lines:       null,
  urlModaleAdministration:null,
  init: function(options){
    Object.extend(PlanSoins, options);
  },

  selColonne: function(hour){
    $(PlanSoins.plan_soin_id).select('div.non_administre:not(.perfusion), div.a_administrer:not(.perfusion)').each(function(oDiv){
      if(oDiv.up("tbody").visible() && oDiv.up("td").hasClassName(hour)){
        if(Object.isFunction(oDiv.onclick)){
          oDiv.onclick();
        }
      }
    });
    
    $(PlanSoins.plan_soin_id).select('div.first_line_mix_item').each(function(oDiv){
      if(oDiv.up("tbody").visible() && oDiv.up("td").hasClassName(hour)){
        oDiv = oDiv.up("div");
        if(Object.isFunction(oDiv.onclick)){
          oDiv.onclick();
        }
      }
    });
  },
  
  oDragOptions: {
    constraint: 'horizontal',
    revert: true,
    //ghosting: true,
    starteffect : function(element) {
      new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
     // element.hide();
    },
    reverteffect: function(element, top_offset, left_offset) {
      element._revert = new Effect.Move(element, { 
        x: -left_offset, 
        y: -top_offset, 
        duration: 0
      } );
     // Suppression des zones droppables sur le revert
     Droppables.drops.clear(); 
     element.show();
    },
    endeffect: function(element) {
      new Effect.Opacity(element, { duration:0.2, from:0.7, to:1.0 } ); 
    }
  },
  
  addAdministration: function(line_id, quantite, key_tab, object_class, dateTime, administrations, planification_id, multiple_adm, lock_hour) {
    /*
     Dans le cas des administrations multiples, si on clique sur la case principale, 
     on selectionne toutes les administrations et on lance la fenetre d'administrations multiples
    */
    if(multiple_adm == 1){
      var date_time = dateTime.replace(' ', '_').substring(0,13);
      $('subadm_'+line_id+'_'+object_class+'_'+key_tab+'_'+date_time).select('div').each(function(e){
        e.onclick.bind(e)();
      });
      PlanSoins.applyAdministrations();
      return;
    }
    
    // Replanification depuis la fenetre
    var replanif = 0;
    if (quantite != '-' && ($V(document.mode_dossier_soin.mode_dossier) == "planification")) {
      replanif = 1;
    }
    
    // On ne permet pas de faire des planifications sur des lignes de medicament    
    if(!replanif && quantite != "-" && !planification_id && (object_class == "CPrescriptionLineMedicament") && ($V(document.mode_dossier_soin.mode_dossier) == "planification") && (PlanSoins.manual_planif == 0)){
      return;
    }
    
    var url = new Url("dPprescription", "httpreq_add_administration");
    url.addParam("line_id",  line_id);
    url.addParam("quantite", quantite);
    url.addParam("key_tab", key_tab);
    url.addParam("object_class", object_class);
    url.addParam("dateTime", dateTime);
    url.addParam("administrations", administrations);
    url.addParam("planification_id", planification_id);
    url.addParam("date_sel", PlanSoins.date);
    url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
    url.addParam("multiple_adm", multiple_adm);
    url.addParam("replanification", replanif);
    url.addParam("lock_hour", lock_hour);
    PlanSoins.urlModaleAdministration = url.requestModal(800,600);
  },
  
  addAdministrationPerf: function(prescription_line_mix_id, date, hour, time_prevue, sejour_id, replanification){
    var mode_dossier = $V(document.mode_dossier_soin.mode_dossier);
    
    var url = new Url("dPprescription", "httpreq_add_administration_perf");
    url.addParam("prescription_line_mix_id", prescription_line_mix_id);
    url.addParam("date", date);
    url.addParam("hour", hour);
    url.addParam("time_prevue", time_prevue);
    url.addParam("mode_dossier", mode_dossier);
    url.addParam("sejour_id", sejour_id);
    url.addParam("date_sel", PlanSoins.date);
    if (mode_dossier == "planification" && replanification) {
      url.addParam("replanification", true);
    }
    PlanSoins.urlModaleAdministration = url.requestModal(800,600);

  },

  addPlanification: function(date, time, key_tab, object_id, object_class, element_id, unite_sans_planif){
    // Split de l'element_id
    var element = element_id.split("_");
    var original_date = element[3]+" "+element[4]+":00:00";
    var quantite = element[5];
    var planification_id = element[6];
  
    // Hack pour corriger le probleme des planifications sur aucune prise prevue
    if(element[2] == 'aucune' && element[3] == 'prise'){
      original_date = element[4]+" "+element[5]+":00:00";
      quantite = element[6];
      planification_id = element[7];
    }
  
    var oForm = document.addPlanif;
    $V(oForm.administrateur_id, User.id);
    
    $V(oForm.object_id, object_id);
    $V(oForm.object_class, object_class);

    var prise_id = !isNaN(key_tab) ? key_tab : '';
    var unite_prise = isNaN(key_tab) ? key_tab : '';

    if (unite_sans_planif) {
      $V(oForm.unite_prise, unite_sans_planif);
    }
    else {
      $V(oForm.unite_prise, unite_prise);
    }
    $V(oForm.prise_id, prise_id);
    $V(oForm.quantite, quantite);
  
    var dateTime = date+" "+time;
    
    $V(oForm.dateTime, dateTime);
    if(planification_id){
      $V(oForm.administration_id, planification_id);
      oForm.original_dateTime.writeAttribute("disabled", "disabled");
    } else { 
      oForm.original_dateTime.enable();
      $V(oForm.original_dateTime, original_date);
    }
    
    if(original_date != dateTime || PlanSoins.manual_planif){
      onSubmitFormAjax(oForm, function(){
        PlanSoins.loadTraitement(null, PlanSoins.date, PlanSoins.nb_decalage, 'planification', object_id, object_class, key_tab);
      });
    }
  },
  
  addPlanifications: function(div, td) {
    var oFormPlanifs = getForm("addPlanifs");
    
    $V(oFormPlanifs.administrateur_id, User.id);
    
    var _div = div.id.split("_");
    var _td = td.id.split("_");
    
    var planification_id = _div[6];
    $V(oFormPlanifs.administrations_ids, planification_id);

    // Nombre d'heures pour le décalage de la replanification, en relatif
    var decalage = _td[5] - _div[4];
      $V(oFormPlanifs.decalage, decalage);
    
    // Ajout de la planification draggée
    var key_tab = td.getAttribute("data-uniteprise");

    var lines_params = [_td[2] + "-"+_td[1] + "-" + key_tab];
    
    // Parcours des sélections sauf celle que l'on drag
    div.up("tbody").select("div.administration-selected").each(function(elt) {
      var tdelt = elt.up("td");
      var _divelt = elt.id.split("_");
      var planification_id = _divelt[6];
      
      if (elt != div && planification_id != "") {
        var _tdelt = tdelt.id.split("_");
        var line_id = _tdelt[1];
        var line_class = _tdelt[2];
        lines_params.push(line_class + "-" + line_id + "-" + tdelt.getAttribute("data-uniteprise"));
        
        $V(oFormPlanifs.administrations_ids, $V(oFormPlanifs.administrations_ids) + "_" + planification_id);
      }
    });

    submitFormAjax(oFormPlanifs, 'systemMsg', {onComplete: function() {
      $A(lines_params).uniq().each(function(line_param) {
        var split = line_param.split("-");
        var line_id = split[1];
        var line_class = split[0];
        var key_tab = split[2];
        PlanSoins.loadTraitement(null,PlanSoins.date, $V(getForm("click").nb_decalage), 'planification', line_id, line_class, key_tab);
      });
    } } );
  },
  
  addPlanificationPerf: function(planif_id, datetime, prescription_line_mix_id, original_datetime){    
    var oFormPlanifPerf = getForm("addManualPlanifPerf");
    $V(oFormPlanifPerf.planification_systeme_id, planif_id);
    $V(oFormPlanifPerf.datetime, datetime);
    $V(oFormPlanifPerf.prescription_line_mix_id, prescription_line_mix_id);
    $V(oFormPlanifPerf.original_datetime, original_datetime);
    
    return onSubmitFormAjax(oFormPlanifPerf, { onComplete: function(){
      PlanSoins.loadTraitement(null, PlanSoins.date, PlanSoins.nb_decalage, 'planification', prescription_line_mix_id, 'CPrescriptionLineMix','');
    } });
  },
  
  
  toggleSelectForAdministration: function (element, line_id, quantite, key_tab, object_class, dateTime) {  
    element = $(element);
  
    // si la case est une administration multiple, on selectionne tous les elements à l'interieur
    if(element.hasClassName('multiple_adm')){
      element.next('div').select('div').invoke('onclick');
    }
    
    if (element._administration) {
      element.removeClassName('administration-selected');
      element._administration = null;
    }
    else {
      element.addClassName('administration-selected');
      element._administration = {
        line_id: line_id,
        quantite: quantite,
        key_tab: key_tab,
        object_class: object_class,
        dateTime: dateTime,
        date_sel: PlanSoins.date
      };
    }
  },
  
  toggleSelectForAdministrationLineMix: function (element, line_id, dateTime) {
    element = $(element);

    if (element._administration) {
      element.removeClassName('administration-selected');
      element._administration = null;
    }
    else {
      element.addClassName('administration-selected');
      element._administration = {
        line_id: line_id,
        dateTime: dateTime,
        date_sel: PlanSoins.date
      };
    }
  },

  reloadSuiviSoin: function(sejour_id, date, hide_old_lines, hide_cond_lines) {
    PlanSoins.loadTraitement(sejour_id, date, null, null, null, null, null, null, null, null, hide_old_lines, hide_cond_lines);
  },

  loadTraitement: function(sejour_id, date, nb_decalage, mode_dossier, object_id, object_class, unite_prise, chapitre, without_check_date, hide_close, hide_old_lines, hide_line_inactive) {
    var url = new Url("soins", "ajax_vw_dossier_soin");
    url.addParam("sejour_id", sejour_id);
    
    url.addParam("date_plan_soins", date);
    
    url.addParam("line_type", "bloc");
    url.addParam("mode_bloc", "0");
    url.addParam("mode_dossier", mode_dossier);
    if(nb_decalage){
      url.addParam("nb_decalage", nb_decalage);
    }
    url.addParam("chapitre", chapitre);
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("unite_prise", unite_prise);
    url.addParam("without_check_date", without_check_date);
    if (hide_close) {
      url.addParam("hide_close", hide_close);
    }
    if (hide_old_lines >= 0) {
      url.addParam("hide_old_lines", hide_old_lines);
    }
    if (hide_line_inactive >= 0) {
      url.addParam("hide_line_inactive", hide_line_inactive);
    }

    if (PlanSoins.regroup_lines !== null) {
      url.addParam("regroup_lines", PlanSoins.regroup_lines ? "1" : "0");
    }

    if(object_id && object_class){
      if(object_class == 'CPrescriptionLineMix'){
        url.requestUpdate("line_"+object_class+"-"+object_id, function() {
          var elt = $("line_"+object_class+"-"+object_id);
          elt.hide();
          PlanSoins.moveDossierSoin(elt);
        });
      }
      else {
        unite_prise = (unite_prise+"").replace(/[^a-z0-9_-]/gi, '_');
        
        var first_td = $('first_'+object_id+"_"+object_class+"_"+unite_prise);
        var last_td = $('last_'+object_id+"_"+object_class+"_"+unite_prise);

        // Suppression des td entre les 2 td bornes
        var td = first_td;
        var colSpan = 0;

        var next;
        while((next = td.next()) && (next.id != last_td.id)){
          if(next.visible()){
            colSpan++;
          }
          next.remove();
        }

        var IE9 = Prototype.Browser.IEDetail.browser == 9;
        if (!IE9) {
          first_td.show();
          first_td.colSpan = colSpan;
        }
                
        url.requestUpdate(first_td, {
          insertion: Insertion.After,
          onComplete: function(){
            PlanSoins.moveDossierSoin($("line_"+object_class+"_"+object_id+"_"+unite_prise), false);
            if (!IE9) {
              first_td.hide();
              first_td.colSpan = 1;
            }
          }
        } );
      }
    } else {
      if (chapitre) {
        if (chapitre == "med" ||
           chapitre == "all_chaps" ||
           chapitre == "all_med" ||
           chapitre == "perfusion" || 
           chapitre == "oxygene" || 
           chapitre == "alimentation" ||
           chapitre == "preparation" ||
           chapitre == "aerosol" ||
           chapitre == "inj" ||
          chapitre == "inscription") {
          chapitre = "_"+chapitre;
        }
        else {
          chapitre = "_cat-"+chapitre;
        }
        var chap = $(chapitre);
        if (chap) {
          url.requestUpdate(chap, {
            onComplete: function() { PlanSoins.moveDossierSoin(chap, false); },
            abortPrevious: true
          } );
        }
      }
      else {
        url.requestUpdate($("dossier_traitement"), {
          abortPrevious: true
        });
      }
    }
  },
  viewDossierSoin: function(element){
    if (PlanSoins.manual_planif) {
      var periode_visible = PlanSoins.composition_dossier[PlanSoins.nb_decalage];
      PlanSoins.toggleManualPlanif(periode_visible);
    }

    // recuperation du mode d'affichage du dossier (administration ou planification)
    if (getForm("mode_dossier_soin") != null) {
      var mode_dossier = $V(document.mode_dossier_soin.mode_dossier);

      // Dossier en mode Administration
      if(mode_dossier == "administration" || mode_dossier == ""){
        $('button_administration').update("Appliquer les administrations sélectionnées");
        element.select('.colorPlanif').invoke("setStyle", {backgroundColor: '#FFD'});
        element.select('.draggablePlanif').each(function(elt){
          elt.removeClassName("draggable");
          elt.onmousedown = null;
        });
        element.select('.canDropPlanif').invoke("removeClassName", "canDrop");
      }

      // Dossier en mode planification
      if(mode_dossier == "planification"){
        $('button_administration').update("Appliquer les planifications sélectionnées");
        element.select('.colorPlanif').invoke("setStyle", {backgroundColor: '#CAFFBA'});

        element.select('.draggablePlanif').each(function(elt){
          elt.addClassName("draggable");

          elt.onmousedown = function(){
            if(elt.hasClassName('perfusion')){
              PlanSoins.addDroppablesPerfDiv(elt);
            } else {
              PlanSoins.addDroppablesDiv(elt);
            }
          }
        });

        element.select('.canDropPlanif').invoke("addClassName", "canDrop");
      }
    }
  },
  
  toggleManualPlanif: function(periode_visible){
    var bornes_dossier = PlanSoins.bornes_composition_dossier;
    var bornes_visibles = bornes_dossier[periode_visible];
    
    $$("div.manual_planif_line").each(function(planifs){
      var margin_left    = 0;
      planifs.select("div.manual_planif").each(function(planif){
        var date = planif.getAttribute("data-datetime");
        if(date >= bornes_visibles["min"] && date <= bornes_visibles["max"]){
          planif.setStyle({
            marginLeft: margin_left + "px"
          });
          planif.show();
          planif.addClassName("planif_poste");
          margin_left = margin_left + 5;
        } else {
          planif.hide();
          planif.removeClassName("planif_poste");
        }
      });
    });
  },
  
  // Deplacement du dossier de soin
  moveDossierSoin: function(element, hack_ie){
    var periode_visible = PlanSoins.composition_dossier[PlanSoins.nb_decalage];
    
    // To prevent IE to crash
    if (hack_ie && Prototype.Browser.IE) {
      element.hide();
    }

    PlanSoins.composition_dossier.each(function(moment){
      element.select('.' + moment).invoke("setVisible", moment == periode_visible);
    });
    
    PlanSoins.viewDossierSoin(element);

    if (hack_ie && Prototype.Browser.IE) {
      element.show();
    }
  },
  
  addDroppablesDiv: function(draggable){
    $(PlanSoins.plan_soin_id).select('.before').each(function(td_before) {
      td_before.onmouseover = function(){
        PlanSoins.timeOutBefore = setTimeout(PlanSoins.showBefore, 1000);
      }
    });
    
    $(PlanSoins.plan_soin_id).select('.after').each(function(td_after) {
      td_after.onmouseover = function(){
        PlanSoins.timeOutAfter = setTimeout(PlanSoins.showAfter, 1000);
      }
    });
    
    $(draggable).up('tr').select('td').each(function(td) {
      if(td.hasClassName("canDrop")){
        Droppables.add(td, {
          onDrop: function(element) {
            // Replanifications multiples
            if (element.up("tbody").select("div.administration-selected").length) {
              PlanSoins.addPlanifications(element, td);
            }
            else {
              var _td = td.id.split("_");
              var line_id = _td[1];
              var line_class = _td[2];
              var unite_prise = td.getAttribute("data-uniteprise");
              var unite_sans_planif = td.getAttribute("data-unite_sans_planif");

              var date = _td[4];
              var hour = _td[5];
              
              // Hack pour corriger le probleme des planifications sur aucune prise prevue
              if(_td[3] == 'aucune' && _td[4] == 'prise'){
                unite_prise = "aucune_prise";
                date = _td[5];
                hour = _td[6];
              }
              // Ajout de la planification
              PlanSoins.addPlanification(date, hour+":00:00", unite_prise, line_id, line_class, element.id, unite_sans_planif);
            }
            // Suppression des zones droppables
            Droppables.drops.clear(); 
            $(PlanSoins.plan_soin_id).select('.before').each(function(td_before) {
              td_before.onmouseover = null;
            });
            $(PlanSoins.plan_soin_id).select('.after').each(function(td_after) {
              td_after.onmouseover = null;
            });
          },
          hoverclass:'soin-selected'
        } );
      } 
    });
  },
  
  // Ajouts des zones droppables pour les perfusions
  addDroppablesPerfDiv: function(draggable){
    $(PlanSoins.plan_soin_id).select('.before').each(function(td_before) {
      td_before.onmouseover = function(){
        PlanSoins.timeOutBefore = setTimeout(PlanSoins.showBefore, 1000);
      }
    });
    $(PlanSoins.plan_soin_id).select('.after').each(function(td_after) {
      td_after.onmouseover = function(){
        PlanSoins.timeOutAfter = setTimeout(PlanSoins.showAfter, 1000);
      }
    });
    
    $(draggable).up('tr').select('td.canDrop').each(function(td) {
        Droppables.add(td, {
          onDrop: function(element) {
            var dateTime = td.get("datetime");
            var prescription_line_mix_id = element.get("prescription_line_mix_id");
            var planif_id = element.get("planif_id");
            var original_dateTime = element.get("original_datetime");
            
            PlanSoins.addPlanificationPerf(planif_id, dateTime, prescription_line_mix_id, original_dateTime);
            // Suppression des zones droppables
            Droppables.drops.clear(); 
            $(PlanSoins.plan_soin_id).select('.before').each(function(td_before) {
              td_before.onmouseover = null;
            });
            $(PlanSoins.plan_soin_id).select('.after').each(function(td_after) {
              td_after.onmouseover = null;
            });
          },
          hoverclass:'soin-selected'
        } );
    });
  },
  
  // Deplacement du dossier vers la gauche
  showBefore: function(){
    if(PlanSoins.nb_decalage >= 1){
      PlanSoins.nb_decalage = parseInt(PlanSoins.nb_decalage) - 1;
      PlanSoins.moveDossierSoin($(PlanSoins.plan_soin_id), true);
    }
    PlanSoins.togglePeriodNavigation();
  },

  // Deplacement du dossier de soin vers la droite
  showAfter: function(){
    if(PlanSoins.nb_decalage < (PlanSoins.nb_postes - 1)){
      PlanSoins.nb_decalage = parseInt(PlanSoins.nb_decalage) + 1;
      PlanSoins.moveDossierSoin($(PlanSoins.plan_soin_id), true);
    }
    PlanSoins.togglePeriodNavigation();
  },

  togglePeriodNavigation: function(){
    if(PlanSoins.nb_decalage >= 1){
      $(PlanSoins.plan_soin_id).select("a.prevPeriod").invoke("show");
    } else {
      $(PlanSoins.plan_soin_id).select("a.prevPeriod").invoke("hide");
    }
    if(PlanSoins.nb_decalage < (PlanSoins.nb_postes - 1)){
      $(PlanSoins.plan_soin_id).select("a.nextPeriod").invoke("show");
    } else {
      $(PlanSoins.plan_soin_id).select("a.nextPeriod").invoke("hide");
    }
  },
  
  applyAdministrations: function () {
    // Initialisation des tableaux
    var administrations = {};
    var administrations_mix = {};
    
    // Parcours des administrations selectionnées  
    $$('div.administration-selected').each(function(element) { 
      if(!element.hasClassName('multiple_adm')){
        var adm = element._administration;
        
        // Medicament ou element
        if(!Object.isUndefined(adm.key_tab)){
          administrations[adm.line_id+'_'+adm.key_tab+'_'+adm.dateTime] = adm; 
        } 
        // Line mix
        else {
          administrations_mix[adm.line_id+'_'+adm.dateTime] = adm; 
        }
      }
    });
    
    $V(getForm("adm_multiple")._administrations, Object.toJSON(administrations));
    $V(getForm("adm_multiple")._administrations_mix, Object.toJSON(administrations_mix));
    
    var url = new Url("dPprescription", "httpreq_add_multiple_administrations");
    url.addParam("mode_dossier", $V(document.mode_dossier_soin.mode_dossier));
    url.addParam("refresh_popup", "1");
    url.popup(700, 600);
  },
  
  addInscription: function(datetime, prescription_id){
    var url = new Url("dPprescription", "vw_edit_inscription");
    url.addParam("datetime", datetime);
    url.addParam("prescription_id", prescription_id);
    url.popup(800, 600, "Ajout d'une inscription");
  },
  
  viewFicheATC: function(fiche_ATC_id) {
    var url = new Url("medicament", "vw_fiche_ATC");
    url.addParam("fiche_ATC_id", fiche_ATC_id);
    url.popup(700, 550, "Fiche ATC");  
  }, 
  
  printBons: function(prescription_id) {
    var url = new Url("prescription", "print_bon");
    url.addParam("prescription_id", prescription_id);
    url.addParam("debut", PlanSoins.date);
    url.popup(900, 600, "Impression des bons");
  },

  printAdministrations: function(prescription_id) {
    var url = new Url("prescription", "print_administrations");
    url.addParam("prescription_id", prescription_id);
    url.popup(900, 600, "Impression des administrations");
  },

  closeAllAlertes: function(chapitre, ampoule, urgence, chapitre_plan_soins){
    // Si le chapitre n'est pas visibe
    if(!$(chapitre_plan_soins).visible()) {
      return;
    }
    
    // Application de toutes les alertes du chapitre
    var form_name = urgence ? 'form-alerte-urgence-' : 'form-alerte-';
    form_name += chapitre;

    $$("form."+form_name).each(function(oForm){
      onSubmitFormAjax(oForm, { onComplete: function(){ 
        // On masque les ampoules pour chaque lignes
        $('alert_manuelle_'+$V(oForm.alert_id)).hide();
        ampoule.hide();
      }});
    });
  },
  
  editTask: function(sejour_id, prescription_line_element_id){
    var url = new Url("soins", "ajax_modal_task");
    url.addParam("sejour_id", sejour_id);
    url.addParam("prescription_line_element_id", prescription_line_element_id);
    url.requestModal(600, 200);
  },

  editRDV: function(patient_id, sejour_id, prescription_line_element_id) {
    var url = new Url("cabinet", "edit_planning");
    url.addParam("consultation_id", 0);
    url.addParam("sejour_id"   , sejour_id);
    url.addParam("pat_id"      , patient_id);
    url.addParam("line_element_id", prescription_line_element_id);
    url.addParam("dialog"      , 1);
    url.modal({width: 1000, height: 700});
    url.modalObject.observe("afterClose", function() {
      PlanSoins.loadTraitement(sejour_id, PlanSoins.date, '', 'administration');
    } );
  },

  refreshTask: function(prescription_line_element_id){
    var url = new Url("soins", "ajax_update_task_icon");
    url.addParam("prescription_line_element_id", prescription_line_element_id);
    url.requestUpdate("show_task_"+prescription_line_element_id);
  },

  askMovePlanifs: function(prise_id, object_id, object_class, datetime, nb_hours, quantite, unite_prise) {
    var oForm = getForm("movePlanifs");
    $V(oForm.prise_id, prise_id == null ? "" : prise_id);
    $V(oForm.object_id, object_id);
    $V(oForm.object_class, object_class);
    $V(oForm.datetime, datetime);
    $V(oForm.nb_hours, nb_hours);
    $V(oForm.quantite, quantite);
    $V(oForm.unite_prise, unite_prise);
    $("modalMovePlanifs").select("button").invoke("writeAttribute", "cancelled", null).invoke("writeAttribute", "disabled", null);
    Modal.open("modalMovePlanifs");
  },

  movePlanifs: function(type_move) {
    var oForm = getForm("movePlanifs");
    $V(oForm.type_move, type_move);

    return onSubmitFormAjax(oForm, { onComplete: function() {
      Control.Modal.close();
      PlanSoins.loadTraitement(null,PlanSoins.date, PlanSoins.nb_decalage, 'planification', $V(oForm.object_id), $V(oForm.object_class), $V(oForm.prise_id) ? $V(oForm.prise_id) : $V(oForm.unite_prise));
    } });
  },

  showPeropAdministrations: function(prescription_id) {
    var url = new Url("soins", "ajax_vw_perop_administrations");
    url.addParam("prescription_id", prescription_id);
    url.requestUpdate("perop_adm");
  },

  startAnciennete : function(chapitre) {
    if (!PlanSoins.anciennete) {
      return;
    }
    PlanSoins.time_anciennete[chapitre] = new Date();
    $(chapitre+"_time").addClassName("opacity-60").setStyle({color: ""});
    PlanSoins.updateAnciennete(chapitre);
    PlanSoins.timer_anciennete[chapitre] = setInterval(PlanSoins.updateAnciennete.curry(chapitre), 60000);
  },

  updateAnciennete: function(chapitre) {
    var span = $(chapitre+"_time");
    if (!span) {
      clearTimeout(PlanSoins.timer_anciennete[chapitre]);
      PlanSoins.time_anciennete[chapitre] = null;
      return;
    }
    var diff = parseInt((new Date() - PlanSoins.time_anciennete[chapitre]) / 60000);
    span.update(diff+" min");

    if (diff > PlanSoins.anciennete) {
      span.removeClassName("opacity-60");
      span.setStyle({color: "#ffd700"});
    }
  },

  toggleAnciennete: function(chapitre) {
    if (!PlanSoins.anciennete) {
      return;
    }
    $$(".anciennete").invoke("hide");
    $(chapitre+"_time").show();
  },

  toggleView: function(view) {
    if (view == 'semaine') {
      $('tab_dossier_soin').down('li.semaine').onmousedown();
      $('jour').hide();
      $('semaine').show();
    }

    if (view == 'jour') {
      $('tab_dossier_soin').down('li.jour').onmousedown();
      $('semaine').hide();
      $('jour').show();
    }
  },

  loadLiteSuivi: function(sejour_id) {
    var url = new Url("soins", "ajax_vw_dossier_suivi_lite");
    url.addParam("sejour_id", sejour_id);
    url.requestUpdate("dossier_suivi_lite");
  },

   showModalAllTrans: function(sejour_id) {
    loadSuivi(sejour_id, null, null, null, null, null, 1);
    var modal_suivi_lite = Modal.open("dossier_suivi", { showClose: true});
    modal_suivi_lite.container.setStyle({width: "80%", height: "80%"});
    Control.Modal.position();
    modal_suivi_lite.observe("afterClose", PlanSoins.loadLiteSuivi.curry(sejour_id));
  },

  showModalTasks: function(sejour_id) {
    updateTasks(sejour_id);
    Modal.open("tasks", { showClose: true, width: 800, height: 600 });
  }
};