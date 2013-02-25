{{mb_script module=urgences script=motif}}
<script>
  Main.add(function() {
    Control.Tabs.create("tabs_motifs", true);
  });
  
  function popupImport() {
  var url = new Url('urgences', 'motif_import_csv');
  url.popup(800, 600, 'Import des motifs d\'urgence');
  }
</script>
<ul id="tabs_motifs" class="control_tabs">
  <li>
    <a href="#chapitres">Chapitres</a>
  </li>
  <li>
    <a href="#motifs">Motifs</a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="chapitres" style="display:none;">
  {{mb_include module=urgences template=vw_list_chapitres}}
</div>

<div id="motifs" style="display:none;">
  {{mb_include module=urgences template=vw_list_motifs}}
</div>
