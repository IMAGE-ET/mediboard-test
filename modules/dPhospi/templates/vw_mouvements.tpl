<script type="text/javascript">
   loadNonPlaces = function(after_refresh) {
     if (!after_refresh) {
       after_refresh = Prototype.emptyFunction;
     }
     var url = new Url("dPhospi", "ajax_vw_non_places");
     url.requestUpdate("list_affectations", {onComplete: after_refresh});
   }
   
   changeLit = function(affectation_id, link_affectation) {
      var url = new Url('dPhospi', 'ajax_suggest_lit');
      url.addParam('affectation_id', affectation_id);
      
      if (link_affectation) {
        url.addParam("_link_affectation", link_affectation);
      }
      
      url.requestModal(700, 400);
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
       
       url.requestUpdate($("CLit-"+lit_id), {onComplete: after_refresh});
     }
     else return onSubmitFormAjax(getForm('filterMouv'), {onComplete: after_refresh}, 'view_affectations');
   }
   
   editAffectation = function(affectation_id, lit_id, urgence) {
     var url = new Url("dPhospi", "ajax_edit_affectation");
     url.addParam("affectation_id", affectation_id);
     
     if (!Object.isUndefined(lit_id)) {
       url.addParam("lit_id", lit_id);
     }
     if (!Object.isUndefined(urgence)) {
      url.addParam("urgence", urgence);
     }
     
     Placement.stop();
     var modal = url.requestModal(500, null, {showReload: false});
     modal.modalObject.observe("afterClose", function() { Placement.resume(); });
   }
   
   moveAffectation = function(affectation_id, lit_id, sejour_id, lit_id_origine) {
   
    var url = new Url("dPhospi", "ajax_move_affectation");
    if (!Object.isUndefined(affectation_id)) {
      url.addParam("affectation_id", affectation_id);
    }
    url.addParam("lit_id", lit_id);
    
    if (!Object.isUndefined(sejour_id)) {
      url.addParam("sejour_id", sejour_id);
    }
    url.requestUpdate("systemMsg", {onComplete: function() {
      var after_mouv = null;
      
      // Pas d'affectation_id ou pas de lit_id_origine (affectation dans un couloir),
      // on recharge la liste des affectations (placement d'un patient)
      if (!affectation_id || !lit_id_origine) {
        after_mouv = loadNonPlaces;
      }
      
      if (lit_id_origine) {
        refreshMouvements(after_mouv, lit_id_origine, after_mouv);
      }
      if (lit_id) {
        refreshMouvements(after_mouv, lit_id, after_mouv);
      }
    }});
  }
  
  // Drop d'une affectation avec la touche ctrl
  selectAction = function(affectation_id, lit_id, sejour_id) {
    Placement.stop();
    var url = new Url("dPhospi", "ajax_select_action_affectation");
    url.addParam("affectation_id", affectation_id);
    url.addParam("lit_id", lit_id);
    url.addParam("sejour_id", sejour_id);
    var modal = url.requestModal(500, null, {showReload: false});
    modal.modalObject.observe("afterClose", function() {  Placement.resume(); });
  }
  
  window.sejour_selected = null;
  window.lit_selected    = null;
  window.affectation_selected = null;
  
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
  
  Main.add(function() {
    Calendar.regField(getForm('filterMouv').date);
    var view_affectations = $("view_affectations");
    view_affectations.setStyle(
      { height: document.viewport.getHeight()*(0.59)+"px"
      });
    var list_affectations = $("list_affectations");
    list_affectations.setStyle(
      { height: document.viewport.getHeight()*(0.3)+"px"
      });
    refreshMouvements(loadNonPlaces.curry(function() {
      var time_line_temporelle = $("time_line_temporelle");
      var time_line_temporelle_na = $("time_line_temporelle_non_affectes");
      window.top_tempo = time_line_temporelle.getStyle("top");
      time_line_temporelle.setStyle({top: window.top_tempo});
      window.top_tempo_na = time_line_temporelle_na.getStyle("top");
      time_line_temporelle_na.setStyle({top: window.top_tempo_na});
    }
    ));
  });
</script>

<form name="filterMouv" action="?" method="get" onsubmit="return false;">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="a" value="ajax_vw_mouvements" />
  
  <!-- Filtre -->
  <div style="width: 100%;">
    Granularité :
    {{foreach from=$granularites item=_granularite}}
      <label>    
        <input type="radio" name="granularite" value="{{$_granularite}}" onclick="refreshMouvements(loadNonPlaces);"
          {{if $_granularite == $granularite}}checked="checked"{{/if}} />
          {{tr}}CService-granularite-{{$_granularite}}{{/tr}}
      </label>
    {{/foreach}}
    
    &mdash;
    
    Date :
    <input type="hidden" class="date" name="date" value="{{$date}}"
      onchange="$V(this.form.date_da, new Date(Date.fromDATE($V(this))).toLocaleDate()); refreshMouvements(loadNonPlaces); ">
    <input type="text" readonly="readonly" name="date_da" />
    
    &mdash;
    
    Vue :
    <label>
      <input type="radio" name="mode_vue_tempo" value="classique" onclick="refreshMouvements()"
        {{if $mode_vue_tempo == "classique"}}checked="checked"{{/if}}/> Classique
    </label>
    <label>
      <input type="radio" name="mode_vue_tempo" value="compacte" onclick="refreshMouvements()"
        {{if $mode_vue_tempo == "compacte"}}checked="checked"{{/if}}/> Compacte
    </label>
    
    &mdash;
    
    Axe de prestation :
    <select name="prestation_id" onchange="savePrefAndReload(this.value);">
      <option value="">&mdash; Tous</option>
      {{foreach from=$prestations_journalieres item=_prestation}}
        <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected="selected"{{/if}}>{{$_prestation->nom}}</option>
      {{/foreach}}
    </select>
    
    <button type="button" class="pause notext" onclick="togglePlayPause(this);"
      title="{{tr}}CAffectation-play_pause_temporel{{/tr}}"></button>
  </div>
  
</form>

<div id="view_affectations" style="overflow-x: auto; overflow-y: scroll;" onscroll="syncBars(this)"></div>
<hr />
<div id="list_affectations" style="overflow-x: auto; overflow-y: scroll;" onscroll="syncBars(this)"></div>