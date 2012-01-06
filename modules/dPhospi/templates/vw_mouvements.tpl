<script type="text/javascript">
   loadNonPlaces = function() {
     var url = new Url("dPhospi", "ajax_vw_affectations");
     url.addParam("offset_th", $$(".first_th")[0].getStyle("width"));
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
    list_affectations.setStyle({ height: document.viewport.getHeight()*(0.3)+"px"});
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
  <input type="hidden" name="a" value="ajax_refresh_mouvements" />
  
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
      <input type="radio" name="vue" value="classique" onclick="refreshMouvements()"
        {{if $vue == "classique"}}checked="checked"{{/if}}/> Classique
    </label>
    <label>
      <input type="radio" name="vue" value="compacte" onclick="refreshMouvements()"
        {{if $vue == "compacte"}}checked="checked"{{/if}}/> Compacte
    </label>
  </div>
  
</form>

<div id="view_affectations" style="overflow-x: auto;" onscroll="syncBars(this)"></div>
<hr />
<div id="list_affectations" style="overflow-x: auto;" onscroll="syncBars(this)"></div>