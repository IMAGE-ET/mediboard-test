<h2>
{{if $consult->_id}}
  {{$consult}}
{{elseif $patient->_id}}
  {{$patient}}
{{/if}}
</h2>

<script type="text/javascript">
toggleDisplay = function(className, status) {
  document.body.select("td." + className).each (function(elt) {
    if (status) {
      elt.show();
    }
    else {
      elt.hide();
    }
  });
  
  // Refaire le count pour le volet Antécédents
  $("antecedents").down("tr", 1).down("td", 1).select("table").each(function(table) {
    var aides = table.select("td."+className);
    var tab = document.body.down("a[href=#"+table.id+"]");
    
    var small = tab.down("small");
    var count = 0;
    if (status) {
      count = parseInt(small.innerHTML.replace(/(\(|\))*/,"")) + aides.length;
    }
    else {
      count = parseInt(small.innerHTML.replace(/(\(|\))*/,"")) - aides.length;
    }
    small.update("("+count+")");
    
    if (count == 0) {
      tab.addClassName("empty");
    }
    else {
      tab.removeClassName("empty");
    }
    
    // Ansin que pour les sous-volets
    table.select("a").each(function(elt) {
      var id = elt.href.split("#")[1];
      var tbody = $(id);
      
      var nb_tds = (tbody.select("td.text").findAll(function(el) { return el.visible(); })).length;
      var tab = document.body.down("a[href=#"+id+"]");
      var small = tab.down("small");
      
      small.update("("+nb_tds+")");
      if (nb_tds == 0) {
        tab.addClassName("empty");
      }
      else {
        tab.removeClassName("empty");
      }
    });
  });
}
Main.add(function () {
  Control.Tabs.create('tab-main', false);
});
</script>

<ul id="tab-main" class="control_tabs">
  <li><a href="#antecedents">Antécédents</a></li>
  <li><a href="#traitements">Traitements</a></li>
  <li>
    <label>
      <input type="checkbox" checked="checked" onclick="toggleDisplay('user', this.checked)"> Utilisateur
    </label>
    <label>
      <input type="checkbox" checked="checked" onclick="toggleDisplay('function', this.checked)"> Fonction
    </label>
    <label>
      <input type="checkbox" checked="checked" onclick="toggleDisplay('group', this.checked)"> Etab.
    </label>
  </li>
</ul>
<hr class="control_tabs" />

{{include file=inc_grid_antecedents.tpl}}
{{include file=inc_grid_traitements.tpl}}

