{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("typeVue").date, null);
  controlTabs        = Control.Tabs.create('tabs-edit-mouvements', true);
  var type           = controlTabs.activeContainer.id.split('_')[0];
  var type_mouvement = controlTabs.activeContainer.id.split('_')[1];
  refreshList(null, null, type, type_mouvement);
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

function refreshList(order_col, order_way, type, type_mouvement) {
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
    if (type_mouvement) {
      url.addParam("type_mouvement", type_mouvement);
    }
  }
  else {
    url.addParam("type", controlTabs.activeContainer.id);
  }
  url.addParam("vue", $V(oForm.vue));
  url.addParam("date", $V(oForm.date));
  if (type) {
    if(type_mouvement) {
      url.requestUpdate(type+"_"+type_mouvement);
    } else {
      url.requestUpdate(type+"_");
    }
  }
  else {
    url.requestUpdate(controlTabs.activeContainer.id);
  }
}

function showDateDeces(sejour_id) {
  $('dateDeces'+sejour_id).show();
}

function selectServices() {
  var url = new Url("hospi", "ajax_select_services");
  url.addParam("view", "mouvements");
  url.addParam("ajax_request", 0);
  url.requestModal(null, null, {maxHeight: '600'});
}
</script>

<table class="main">
  <tr>
    <td>
        <form name="typeVue" action="?" method="get">
          <input type="hidden" name="m" value="dPhospi" />
          <input type="hidden" name="tab" value="edit_sorties" />
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          <select name="type_hospi" style="width: 13em;" onchange="this.form.submit()">
            <option value="">&mdash; Type d'hospitalisation</option>
            {{foreach from=$mouvements item=_mouvement key=type}}
            <option value="{{$type}}" {{if $type == $type_hospi}}selected="selected"{{/if}}>
              {{tr}}CSejour._type_admission.{{$type}}{{/tr}}
            </option>
            {{/foreach}}
          </select>
          <select name="praticien_id" style="width: 13em;" onchange="this.form.submit()">
            <option value="">&mdash; Praticien</option>
            {{foreach from=$praticiens item=_prat}}
            <option value="{{$_prat->_id}}" {{if $_prat->_id == $praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
            {{/foreach}}
          </select>
          <select name="vue" style=" width: 12em;" onchange="this.form.submit()">
            <option value="0" {{if $vue == 0}} selected="selected"{{/if}}>Afficher les validés</option>
            <option value="1" {{if $vue == 1}} selected="selected"{{/if}}>Ne pas afficher les validés</option>
          </select>
          <button type="button" onclick="selectServices();" class="search">Services</button>
        </form>
    </td>
  </tr>
</table>

<ul id="tabs-edit-mouvements" class="control_tabs">
  {{foreach from=$mouvements item=_mouvement key=type}}
  {{foreach from=$_mouvement item=_liste key=type_mouvement}}
    {{if $_liste.place || $_liste.non_place}}
    <li onmousedown="refreshList(null, null, '{{$type}}', '{{$type_mouvement}}')">
      <a href="#{{$type}}_{{$type_mouvement}}">
        {{if $type == "ambu"}}
          {{tr}}CSejour.type.{{$type}}{{/tr}}
        {{else}}
          {{tr}}CSejour.type_mouvement.{{$type_mouvement}}{{/tr}} {{tr}}CSejour.type.{{$type}}{{/tr}}
        {{/if}}
        <small id="count_{{$type}}_{{$type_mouvement}}">({{$_liste.place}}/{{$_liste.non_place}})</small>
      </a>
    </li>
    {{/if}}
  {{/foreach}}
  {{/foreach}}
  <li onmousedown="refreshList(null, null, 'deplacements')">
    <a href="#deplacements_">Déplacements <small id="count_deplacements_">({{$dep_entrants}}/{{$dep_sortants}})</small></a>
  </li>
  {{if $type != "ambu"}}
  <li onmousedown="refreshList(null, null, 'presents')">
    <a href="#presents_">Patients présents <small id="count_presents_">({{$presents}}/{{$presentsNP}})</small></a>
  </li>
  {{/if}}
</ul>

<hr class="control_tabs" />

{{foreach from=$mouvements item=_mouvement key=type}}
{{foreach from=$_mouvement item=_liste key=type_mouvement}}
  {{if $_liste}}
  <div id="{{$type}}_{{$type_mouvement}}" style="display: none;"></div>
  </div>
  {{/if}}
{{/foreach}}
{{/foreach}}

<div id="deplacements_" style="display: none;">
</div>

{{if $type != "ambu"}}
<div id="presents_" style="display: none;">
</div>
{{/if}}
