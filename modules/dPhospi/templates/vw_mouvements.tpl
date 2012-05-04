<script type="text/javascript">
   loadNonPlaces = function() {
     var url = new Url("dPhospi", "ajax_vw_non_places");
     url.requestUpdate("list_affectations");
   }
   
   changeLit = function(affectation_id, sejour_id, link_affectation) {
      var url = new Url('dPhospi', 'ajax_suggest_lit');
      
      if (affectation_id) {
        url.addParam('affectation_id', affectation_id);
      }
      
      if (sejour_id) {
        url.addParam("sejour_id", sejour_id);
      }
      
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
   
   delAffectation = function(affectation_id, lit_id, sejour_guid) {
     var form = getForm("delAffect");
     $V(form.affectation_id, affectation_id);
     
     return onSubmitFormAjax(form, {onComplete: function(){
       refreshMouvements(loadNonPlaces, lit_id);
       if (sejour_guid) {
         $("view_affectations").select("."+sejour_guid).each(function(div) {
           var div_lit_id = div.get("lit_id");
           if (div_lit_id != lit_id) {
             refreshMouvements(null, div_lit_id);
           }
         });
       }
     }});
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
      
      // Pas d'affectation_id, on recharge la liste des affectations (placement d'un patient)
      if (!affectation_id) {
        after_mouv = loadNonPlaces;
      }
      if (lit_id_origine) {
        refreshMouvements(null, lit_id_origine);
      }
      refreshMouvements(after_mouv, lit_id);
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
  
  Main.add(function() {
    Calendar.regField(getForm('filterMouv').date);
    var view_affectations = $("view_affectations");
    view_affectations.setStyle(
      { width: document.viewport.getWidth()*(87/100)+"px",
        height: document.viewport.getHeight()*(0.6)+"px"
      });
    var list_affectations = $("list_affectations");
    list_affectations.setStyle(
      { width: document.viewport.getWidth()*(87/100)+"px",
        height: document.viewport.getHeight()*(0.3)+"px"
      });
    refreshMouvements(loadNonPlaces);
  });
</script>

<!-- Formulaire de suppression d'affectation -->
<form name="delAffect" method="post" action="?">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="affectation_id" value="" />
</form>

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
    <select name="prestation_id" onchange="refreshMouvements(loadNonPlaces)">
      <option value="">&mdash; Tous les niveaux</option>
      {{foreach from=$prestations_journalieres item=_prestation}}
        <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected="selected"{{/if}}>{{$_prestation->nom}}</option>
      {{/foreach}}
    </select>
  </div>
  
</form>

<div id="view_affectations" style="overflow-x: auto;" onscroll="syncBars(this)"></div>
<hr />
<div id="list_affectations" style="overflow-x: auto;" onscroll="syncBars(this)"></div>