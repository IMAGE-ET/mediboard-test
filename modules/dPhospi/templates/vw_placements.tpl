{{mb_script module=hospi script=affectation_uf}}

<script>
  Position.includeScrollOffsets = true;
  Placement = {
    tabs: null,
    updater: null,
    frequency: null,
    scrollAffectations: 0,
    scrollNonPlaces: 0,
    loadTableau: function(services_ids) {
      new Url('hospi', 'vw_affectations').requestUpdate('tableau');
    },
    loadTemporel: function() {
      new Url('hospi', 'vw_mouvements').requestUpdate('temporel');
    },
    loadTopologique: function() {
      new Url('hospi', 'vw_placement_patients').requestUpdate('topologique');
    },
    showLegend: function() {
      Modal.open("legend_" + this.tabs.activeLink.key);
    },
    selectServices: function(view, services_ids_suggest) {
      var url = new Url("hospi", "ajax_select_services");
      
      if (Object.isUndefined(view)) {
        view = this.tabs.activeLink.key;
      }
      
      if (!Object.isUndefined(services_ids_suggest)) {
        url.addParam("services_ids_suggest", services_ids_suggest);
      }
      
      url.addParam("view", view);
      url.requestModal(null, null, {maxHeight: '600'});
    },
    loadActiveView: function() {
      switch (this.tabs.activeLink.key) {
        case 'tableau':
          this.loadTableau();
          break;
        case 'temporel':
          this.loadTemporel();
        case 'topologique':
          this.loadTopologique();
      }
    },
    init: function(frequency){
      this.frequency = frequency || this.frequency;
      
      var url = new Url("hospi", "vw_mouvements");
      Placement.updater = url.periodicalUpdate('temporel', {
        frequency: this.frequency,
        onCreate: function() {
          /* On vide la mémoire avant de rafraîchir */
          Droppables.reset();
          Draggables.drags.each(function(elt) {
            Draggables.unregister(elt);
          });
          {{if "reservation"|module_active}}
            var tableau_vue_temporel = $("tableau_vue_temporel");
            if (tableau_vue_temporel) {
              tableau_vue_temporel.select(".mouvement_lit").invoke("stopObserving", "dblclick");
            }
          {{/if}}
          
          var view_affectations = $("view_affectations");
          var list_affectations = $("list_affectations");
          if (!view_affectations || !list_affectations) {
            return;
          }
          Placement.scrollAffectations = view_affectations.scrollTop;
          Placement.scrollNonPlaces    = list_affectations.scrollTop;
        }
      });
    },
    
    start: function(delay, frequency){
      this.stop();
      this.init.delay(delay, frequency);
    },
    
    stop: function(){
      if (this.updater) {
        this.updater.stop();
      }
    },
    
    resume: function(){
      if (this.updater) {
        this.updater.resume();
      }
    }
  }
  
  filter = function(input, table) {
    var alerte = $("alerte_"+table);
    table = $(table);

    var term = $V(input);
    
    if (!term) {
      table.select("tr.line").invoke("show");
      alerte.hide();
      return;
    }

    table.select("tr.line").invoke("hide");
    alerte.show();
    
    table.select(".CPatient-view").each(function(e) {
      if (e.innerHTML.like(term)) {
        e.up("tr.line").show();
      }
    });
  }
  
  loadNonPlaces = function(after_refresh) {
    after_refresh = after_refresh || Prototype.emptyFunction;
    var url = new Url("hospi", "ajax_vw_non_places");
    url.requestUpdate("list_affectations", {onComplete: after_refresh});
  }
 
  changeLit = function(affectation_id, link_affectation, datetime) {
    var url = new Url('hospi', 'ajax_suggest_lit');
    url.addParam('affectation_id', affectation_id);
    url.addParam("datetime", datetime);
    if (link_affectation) {
      url.addParam("_link_affectation", link_affectation);
    }
    
    url.requestModal(700, 400);
  }
  
  editAffectation = function(affectation_id, lit_id, urgence) {
     var url = new Url("hospi", "ajax_edit_affectation");
     url.addParam("affectation_id", affectation_id);
     
     if (!Object.isUndefined(lit_id)) {
       url.addParam("lit_id", lit_id);
     }
     if (!Object.isUndefined(urgence)) {
      url.addParam("urgence", urgence);
     }
     
     Placement.stop();
     url.requestModal(500, null, {showReload: false, onClose: Placement.resume});
   }
   
   moveAffectation = function(affectation_id, lit_id, sejour_id, lit_id_origine) {
    var url = new Url("hospi", "ajax_move_affectation");
    if (!Object.isUndefined(affectation_id)) {
      url.addParam("affectation_id", affectation_id);
    }
    url.addParam("lit_id", lit_id);
    
    if (!Object.isUndefined(sejour_id)) {
      url.addParam("sejour_id", sejour_id);
    }
    url.requestUpdate("systemMsg", function() {
      var after_mouv = null;
      
      // Pas d'affectation_id ou pas de lit_id_origine (affectation dans un couloir),
      // on supprime l'affectation ciblée dans la liste des affectations (placement d'un patient)
      if (!affectation_id || !lit_id_origine) {
        after_mouv = delLine(affectation_id ? affectation_id : sejour_id);
      }
      
      if (lit_id_origine) {
        refreshMouvements(after_mouv, lit_id_origine, after_mouv);
      }
      if (lit_id) {
        refreshMouvements(after_mouv, lit_id, after_mouv);
      }
    });
  }

  delLine = function(object_id) {
    var line = $("wrapper_line_"+object_id);
    if (line) {
      var div = line.down("div");
      var ids = div.get("affectations_enfant");
      if (ids.length > 0) { 
        ids.split("-").each(function(id) {
          $("wrapper_line_"+id).up("tr.line").remove();
        });
      }
      line.up("tr.line").remove();
    }
  }

  // Drop d'une affectation avec la touche ctrl
  selectAction = function(affectation_id, lit_id, sejour_id) {
    Placement.stop();
    var url = new Url("hospi", "ajax_select_action_affectation");
    url.addParam("affectation_id", affectation_id);
    url.addParam("lit_id", lit_id);
    url.addParam("sejour_id", sejour_id);
    url.requestModal(500, null, {showReload: false, onClose: Placement.resume});
  }
  
  chooseSejour = function(sejour_id) {
    window.sejour_selected = sejour_id;
    moveByRadio();
  }
  
  chooseLit = function(lit_id) {
    window.lit_selected = lit_id;
    moveByRadio();
  }
  
  chooseAffectation = function(affectation_id) {
    window.affectation_selected = affectation_id;
    moveByRadio();
  }
  
  // Placement par les boutons radio
  moveByRadio = function() {
    if (window.sejour_selected && window.lit_selected) {
      moveAffectation(null, window.lit_selected, window.sejour_selected);
      window.sejour_selected = null;
      window.lit_selected = null;
    }
    else if (window.lit_selected && window.affectation_selected) {
      moveAffectation(window.affectation_selected, window.lit_selected);
      $("affectation_temporel_"+window.affectation_selected).remove();
      window.affectation_selected = null;
      window.lit_selected = null;
    }
  }
  
  syncBars = function(pre) {
    other = pre.id == 'view_affectations' ? $("list_affectations") : $("view_affectations");
    other.scrollLeft = pre.scrollLeft;
  }
  
  makeDragVisible = function(container,element) {
    if(!container || !element) { return false; }
        var i=$(container).getStyle('width');
        i=i.replace('px','');
        i=Math.round(i-20)+'px';
        element.setStyle({'width':i,'z-index':2000,'position':'absolute','cursor':'move'});

    $(container).setStyle({});
  }
  
  togglePlayPause = function(button) {
    button.toggleClassName("play");
    button.toggleClassName("pause");
    if (button.hasClassName("play")) {
      Placement.stop();
    }
    else {
      Placement.resume();
    }
  }
  
  savePrefAndReload = function(prestation_id) {
    var oForm = getForm("editPrefPresta");
    $V(oForm.elements["pref[prestation_id_hospi]"], prestation_id);
    return onSubmitFormAjax(oForm, {onComplete: function() {
      refreshMouvements(loadNonPlaces);
    }});
  }
  
  createAffectation = function(sejour_id, lit_id) {
    var url = new Url("planningOp", "ajax_create_affectation");
    url.addParam("sejour_id", sejour_id);
    url.addParam("lit_id", lit_id);
    url.requestUpdate("systemMsg", function() {
      refreshMouvements(null, lit_id);
    });
  }
  
  createIntervention = function() {
    Placement.stop();
    var url = new Url("planningOp", "vw_edit_urgence");
    url.addParam("date_urgence", window.save_date);
    url.addParam("hour_urgence", window.save_hour);
    url.addParam("min_urgence" , "00");
    url.addParam("dialog", 1);
    url.addParam("operation_id", 0);
    url.modal({
      width: "95%",
      height: "95%",
      onClose: function() {
        Placement.resume();
        if (window.sejour_id_for_affectation) {
          createAffectation(window.sejour_id_for_affectation, window.save_lit_guid.split("-")[1]);
        }
    } });
  }
  
  createSejour = function() {
    Placement.stop();
    var url = new Url("planningOp", "vw_edit_sejour");
    url.addParam("date_reservation", window.save_date);
    url.addParam("sejour_id", 0);
    url.addParam("dialog", 1);
    url.modal({
      width: "95%",
      height: "95%",
      onClose:function() {
        Placement.resume();
        if (window.sejour_id_for_affectation) {
          createAffectation(window.sejour_id_for_affectation, window.save_lit_guid.split("-")[1]);
        }
      }
    });
  }
  
  chooseIntervSejour = function() {
    window.sejour_id_for_affectation = null;
    Modal.open("choose_interv_sejour");
  }
  
  refreshMouvements = function(after_refresh, lit_id) {
    if (!after_refresh) {
      after_refresh = Prototype.emptyFunction;
    }
    if (lit_id) {
      var url = new Url("dPhospi", "ajax_refresh_line_lit");
      url.addParam("lit_id", lit_id);
      var form = getForm('filterMouv');
      url.addParam("date", $V(form.date));
      url.addParam("mode_vue_tempo", $V(form.mode_vue_tempo));
      url.addParam("prestation_id", $V(form.prestation_id));
      url.addParam("granularite", $V(form.granularite));
      
      url.requestUpdate($("CLit-"+lit_id), function() {
        after_refresh();
        {{if !$readonly && "reservation"|module_active}}
          $("CLit-"+lit_id).select("td").each(function(elt) {
            elt.observe("dblclick", function() {
              var datetime = elt.get("date").split(" ");
              window.save_date = datetime[0];
              window.save_hour = datetime[1].split(":")[0];
              window.save_lit_guid = elt.up("tr").id;
              chooseIntervSejour();
            });
          });
        {{/if}}
      });
    }
    else return onSubmitFormAjax(getForm('filterMouv'), after_refresh, 'view_affectations');
  }

  changeAffService = function(object_id, object_class) {
    var form = getForm("changeService");
    switch (object_class) {
      case "CSejour":
        $V(form.m ,"planningOp");
        $V(form.dosql ,"do_sejour_aed");
        $V(form.sejour_id ,object_id);
        break;
      case "CAffectation":
        $V(form.m ,"hospi");
        $V(form.affectation_id, object_id);
        $V(form.dosql ,"do_affectation_aed");
    }
    var url = new Url("hospi", "ajax_select_service");
    url.addParam("action", "changeService");
    url.requestModal(null, null, {maxHeight: '600'});
  }

  Main.add(function(){
    Placement.tabs = Control.Tabs.create('placements_tabs', true);
    if (Placement.tabs.activeLink.key == "temporel") {
      Placement.start(0, 120);
    }
    else {
      Placement.loadActiveView();
    }
  });
</script>

<!-- Formulaire de sauvegarde de l'axe de prestation en préférence utilisateur (vue temporelle)-->
<form name="editPrefPresta" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="pref[prestation_id_hospi]" value="" />
</form>

<!-- Formulaire de changement de service d'une affectation dans un couloir (pas de lit_id) -->
<form name="changeService" method="post">
  <input type="hidden" name="m" />
  <input type="hidden" name="dosql" />
  <input type="hidden" name="affectation_id" />
  <input type="hidden" name="sejour_id" />
  <input type="hidden" name="service_id" />
</form>

<!-- Légendes -->
<div id="legend_temporel" style="display: none;">
  {{mb_include module=hospi template=inc_legend_mouvement}}
</div>

<div id="legend_tableau" style="display: none;">
  {{mb_include module=hospi template=legende}}
</div>

{{if $conf.dPhospi.use_vue_topologique}}
  <div id="legend_topologique" style="display: none;">
    {{mb_include module=hospi template=legende_topologique}}
  </div>
{{/if}}

<ul class="control_tabs" id="placements_tabs">
  <li onmousedown="Placement.loadTableau();">
    <a href="#tableau">Tableau</a>
  </li>
  <li onmousedown="Placement.start(0, 120);">
    <a href="#temporel">Temporel</a>
  </li>
  {{if $conf.dPhospi.use_vue_topologique}}
  <li onmousedown="Placement.loadTopologique();">
    <a href="#topologique">Topologique</a>
  </li>
  {{/if}}
  <li>
    <button type="button" onclick="Placement.selectServices();" class="search">Services</button>
  </li>
  <li style="float: right">
    <button type="button" onclick="Placement.showLegend();" class="search">Légende</button>
  </li>
</ul>

<hr class="control_tabs" />

<div id="tableau" style="display: none;"></div>
<div id="temporel" style="display: none;"></div>

{{if $conf.dPhospi.use_vue_topologique}}
  <div id="topologique" style="display: none;"></div>
{{/if}}