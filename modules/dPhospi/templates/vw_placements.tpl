{{mb_script module=dPhospi script=affectation_uf}}

<script type='text/javascript'>
  Position.includeScrollOffsets = true;
  Placement = {
    tabs: null,
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
    selectServices: function() {
      var url = new Url("dPhospi", "ajax_select_services");
      url.addParam("view", this.tabs.activeLink.key);
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
    }
  }
  
  Main.add(function(){
    Placement.tabs = Control.Tabs.create('placements_tabs', true);
    Placement.loadActiveView();
  });
</script>

<!-- Légendes -->
<div class="modal" id="legend_temporel" style="display: none;">
  {{mb_include module=dPhospi template=inc_legend_mouvement}}
</div>

<div class="modal" id="legend_tableau" style="display: none;">
  {{mb_include module=dPhospi template=legende}}
</div>

<ul class="control_tabs" id="placements_tabs">
  <li onmousedown="Placement.loadTableau();">
    <a href="#tableau">Tableau</a>
  </li>
  <li onmousedown="Placement.loadTemporel();">
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
