<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("typeVue").date, null);
  controlTabs = new Control.Tabs.create('tabs-edit-sorties', true);
  refreshList(null, null, controlTabs.activeContainer.id);
});

function saveSortie(oFormSortie, oFormAffectation){
  if(oFormSortie) {
    oFormAffectation.sortie.value = oFormSortie.sortie.value;
  }
}

function addDays(button, days) {
  var sortie = button.form.sortie_prevue;
  $V(sortie, Date.fromDATETIME($V(sortie)).addDays(days).toDATETIME());
}

function refreshList(order_col, order_way, type) {
  var oForm = getForm("typeVue");
  var url = new Url("dPhospi", "ajax_list_sorties");
  if (order_col) {
    url.addParam("order_col", order_col);
  }
  if (order_way) {
    url.addParam("order_way", order_way);
  }
  if (type) {
    url.addParam("type", type);
  }
  else {
    url.addParam("type", controlTabs.activeContainer.id);
  }
  url.addParam("vue", $V(oForm.vue));
  url.addParam("date", $V(oForm.date));
  if (type) {
    url.requestUpdate(type);
  }
  else {
    url.requestUpdate(controlTabs.activeContainer.id);
  }
}
</script>

<table class="main">
  <tr>
    <th>
        <form name="typeVue" action="?" method="get">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="tab" value="edit_sorties" />
          <label for="vue" title="Choisir un type de vue">Type de vue</label>
          <select name="vue" onchange="this.form.submit()">
            <option value="0" {{if $vue == 0}} selected="selected"{{/if}}>Tout afficher</option>
            <option value="1" {{if $vue == 1}} selected="selected"{{/if}}>Ne pas afficher les validés</option>
          </select>
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        </form>
    </th>
  </tr>
</table>

<ul id="tabs-edit-sorties" class="control_tabs">
  {{foreach from=$sorties item=_sorties key=type}}
    {{if $_sorties}}
    <li onmousedown="refreshList(null, null, '{{$type}}')">
      <a href="#{{$type}}">Sorties {{tr}}CSejour.type.{{$type}}{{/tr}} prévues (<span id="count_{{$type}}">{{$_sorties}}</span>)</a>
    </li>
    {{/if}}
  {{/foreach}}
  <li onmousedown="refreshList(null, null, 'deplacements')">
    <a href="#deplacements">Déplacements prévus (<span id="count_deplacements">{{$deplacements}}</span>)</a>
  </li>
  <li onmousedown="refreshList(null, null, 'presents')">
    <a href="#presents">Patients présents (<span id="count_presents">{{$presents}}</span>)</a>
  </li>
</ul>

<hr class="control_tabs" />

{{foreach from=$sorties item=_sorties key=type}}
  {{if $_sorties}}
  <div id="{{$type}}" style="display: none;"></div>
  </div>
  {{/if}}
{{/foreach}}

<div id="deplacements" style="display: none;">
</div>

<div id="presents" style="display: none;">
</div>