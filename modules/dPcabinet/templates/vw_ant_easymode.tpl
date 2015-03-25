<h2>
{{if $consult->_id}}
  {{$consult}}
{{elseif $patient->_id}}
  {{$patient}}
{{/if}}
</h2>

<script>
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
      var tab = document.body.down("a[href=#"+table.id+"]");

      if (Object.isUndefined(tab)) {
        return;
      }

      var aides = table.select("td."+className);
      var small = tab.down("small");
      var count = parseInt(small.innerHTML.replace(/(\(|\))*/, ""));

      if (status) {
        count += aides.length;
      }
      else {
        count -= aides.length;
      }

      if (count < 0) {
        count = 0;
      }

      small.update("("+count+")");

      if (count == 0) {
        tab.addClassName("empty");
      }
      else {
        tab.removeClassName("empty");
      }

      // Ainsi que pour les sous-volets
      table.select("a").each(function(elt) {
        var id = elt.href.split("#")[1];
        var tbody = $(id);

        var nb_tds = (tbody.select("td.text").findAll(function(el) { return el.visible(); })).length;
        var tab = document.body.down("a[href=#"+id+"]");

        if (Object.isUndefined(tab)) {
          return;
        }

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
  };
  Main.add(function () {
    Control.Tabs.create('tab-main', false);
  });
</script>

<ul id="tab-main" class="control_tabs">
  <li><a href="#antecedents">{{tr}}CAntecedent.more{{/tr}}</a></li>
  <li><a href="#traitements">Traitements</a></li>
  <li>
    <label>
      <input type="checkbox" checked onclick="toggleDisplay('user', this.checked)"> Utilisateur
    </label>
    <label>
      <input type="checkbox" checked onclick="toggleDisplay('function', this.checked)"> Fonction
    </label>
    <label>
      <input type="checkbox" checked onclick="toggleDisplay('group', this.checked)"> Etab.
    </label>
  </li>
</ul>

{{mb_include template=inc_grid_antecedents}}
{{mb_include template=inc_grid_traitements}}

