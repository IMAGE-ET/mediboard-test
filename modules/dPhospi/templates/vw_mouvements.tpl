<script type="text/javascript">
  dragOptions = {
    starteffect: function(element) {
      new Effect.Opacity(element, { duration:0.2, from:1.0, to:0.7 });
      // Si le patient n'est pas placé, on redimensionne la div à la largeur du séjour
      var width = element.get("width");
      var lit_id = element.get("lit_id");
      
      // Pour les admissions de la veille, la width est négative.
      if (!lit_id && width > 0) {
        element.setStyle({width: (width*45 - 5)+"px"});
      }
    },
    endeffect: function(element) {
      var toOpacity = Object.isNumber(element._opacity) ? element._opacity : 1.0;
      new Effect.Opacity(element, {duration:0.2, from:0.7, to:toOpacity,
        queue: {scope:'_draggable', position:'end'},
        afterFinish: function(){
          Draggable._dragging[element] = false
        }
      });
      if (!element.get("lit_id")) {
        element.setStyle({width: "15em"});
      }
    },
    revert: "true",
    // Permet de scroller en draggant une affectation sur le haut ou le bas de la div contenant le planning
    scroll: "view_affectations"
  };

   loadNonPlaces = function() {
     var url = new Url("dPhospi", "ajax_vw_affectations");
     url.requestUpdate("list_affectations");
   }
   
   refreshMouvements = function(after_refresh) {
     if (!after_refresh) {
       after_refresh = Prototype.emptyFunction;
     }
     return onSubmitFormAjax(getForm('filterMouv'), {onComplete: after_refresh}, 'view_affectations');
   }
   
   editAffectation = function(affectation_id, lit_id) {
     var url = new Url("dPhospi", "ajax_edit_affectation");
     url.addParam("affectation_id", affectation_id);
     if (!Object.isUndefined(lit_id)) {
       url.addParam("lit_id", lit_id);
     }
     var modal = url.requestModal(500, null, {showReload: false});
     modal.modalObject.observe("afterClose", function() { refreshMouvements(loadNonPlaces);});
   }
   
   delAffectation = function(affectation_id) {
     var form = getForm("delAffect");
     $V(form.affectation_id, affectation_id);
     return onSubmitFormAjax(form, {onComplete: function(){ refreshMouvements(loadNonPlaces); }});
   }
   
   moveAffectation = function(affectation_id, lit_id, sejour_id) {
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
      refreshMouvements(after_mouv);
    }});
  }
  
  // Drop d'une affectation avec la touche ctrl
  selectAction = function(affectation_id, lit_id, sejour_id) {
    var url = new Url("dPhospi", "ajax_select_action_affectation");
    url.addParam("affectation_id", affectation_id);
    url.addParam("lit_id", lit_id);
    url.addParam("sejour_id", sejour_id);
    var modal = url.requestModal(500, null, {showReload: false});
    modal.modalObject.observe("afterClose", refreshMouvements);
  }
  
  var sejour_selected = null,
      lit_selected    = null;
  
  chooseSejour = function(sejour_id) {
    var sejour = $("sejour_move_"+sejour_id);
    /*if (sejour.checked) {
      sejour_selected = null;
      sejour.checked = false;
      return;
    }*/
    sejour_selected = sejour_id;
    moveByRadio();
  }
  
  chooseLit = function(lit_id) {
    var lit = $("lit_move_"+lit_id);
    /*if (lit.checked) {
      lit_selected = null;
      lit.checked = false;
      return;
    }*/
    lit_selected = lit_id;
    moveByRadio();
  }
  
  // Placement par les boutons radio
  moveByRadio = function() {
    if (sejour_selected && lit_selected) {
      moveAffectation(null, lit_selected, sejour_selected);
      sejour_selected = lit_selected = null;
    }
  }
  
  Main.add(function() {
    Calendar.regField(getForm('filterMouv').date);
    var view_affectations = $("view_affectations");
     view_affectations.setStyle(
       { width: document.viewport.getWidth()*(87/100)+"px",
         height: document.viewport.getHeight()*(2/3)+"px"
       });
    refreshMouvements();
    loadNonPlaces();
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
  <input type="hidden" name="a" value="ajax_refresh_mouvements" />
  
  <!-- Filtre -->
  <div style="width: 100%;">
    Granularité :
    {{foreach from=$granularites item=_granularite}}
      <label>    
        <input type="radio" name="granularite" value="{{$_granularite}}" onclick="refreshMouvements();"
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
    <select name="vue" onchange="refreshMouvements()">
      <option value="classique" {{if $vue == "classique"}}selected="selected"{{/if}}>Classique</option>
      <option value="compacte" {{if $vue == "compacte"}}selected="selected"{{/if}}>Compacte</option>
    </select>
  </div>
  
</form>

<div style="overflow: auto;" id="view_affectations"></div>
<div id="list_affectations"></div>