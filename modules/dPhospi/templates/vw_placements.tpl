{{mb_script module=hospi script=affectation_uf}}

<script type='text/javascript'>
  Position.includeScrollOffsets = true;
  Placement = {
    tabs: null,
    updater: null,
    frequency: null,
    scrollAffectations: 0,
    scrollNonPlaces: 0,
    loadTableau: function(services_ids) {
      var url = new Url('dPhospi', 'vw_affectations');
      url.requestUpdate('tableau');
    },
    loadTemporel: function() {
      var url = new Url('dPhospi', 'vw_mouvements');
      url.requestUpdate('temporel');
    },
    showLegend: function() {
      modal("legend_" + this.tabs.activeLink.key);
    },
    selectServices: function(view, services_ids_suggest) {
      var url = new Url("dPhospi", "ajax_select_services");
      
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
      }
    },
    init: function(frequency){
      this.frequency = frequency || this.frequency;
      
      var url = new Url("dPhospi", "vw_mouvements");
      Placement.updater = url.periodicalUpdate('temporel', {
        frequency: this.frequency,
        onCreate: function() {
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
    table.select("tr").invoke("show");
    
    var term = $V(input);
    
    if (!term) {
      alerte.hide();
      return;
    }
    
    alerte.show();
    
    table.select(".CPatient-view").each(function(e) {
      if (!e.innerHTML.like(term)) {
        e.up("tr.line").hide();
      }
    });
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

<!-- Légendes -->
<div class="modal" id="legend_temporel" style="display: none;">
  {{mb_include module=hospi template=inc_legend_mouvement}}
</div>

<div class="modal" id="legend_tableau" style="display: none;">
  {{mb_include module=hospi template=legende}}
</div>

<ul class="control_tabs" id="placements_tabs">
  <li onmousedown="Placement.loadTableau();">
    <a href="#tableau">Tableau</a>
  </li>
  <li onmousedown="Placement.start(0, 120);">
    <a href="#temporel">Temporel</a>
  </li>
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
