{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

<script>
  Main.add(function () {
    Calendar.regField(getForm("typeVue").date, null);
    controlTabs        = Control.Tabs.create('tabs-edit-mouvements', true);
    var type           = controlTabs.activeContainer.id.split('_')[0];
    var type_mouvement = controlTabs.activeContainer.id.split('_')[1];
    refreshList(null, null, type, type_mouvement);
  });

  function saveSortie(oFormSortie, oFormAffectation) {
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
    var formAff = getForm("chgAff");
    var url = new Url("hospi", "ajax_list_sorties");
    url.addNotNullParam("order_col", order_col);
    url.addNotNullParam("order_way", order_way);
    if (formAff) {
      url.addParam("mode", $V(formAff.mode));
      url.addParam("hour_instantane", $V(formAff.hour_instantane));
    }
    else {
      url.addParam("mode", '{{$mode}}');
      url.addParam("hour_instantane", '{{$hour_instantane}}');
    }
    if (type) {
      url.addParam("type", type);
      url.addNotNullParam("type_mouvement", type_mouvement);
    }
    else {
      url.addParam("type", controlTabs.activeContainer.id);
    }
    url.addParam("vue", $V(oForm.vue));
    url.addParam("date", $V(oForm.date));
    url.addParam("prestation_id", $V(oForm.prestation_id));
    if (type) {
      if (type_mouvement) {
        url.requestUpdate(type+"_"+type_mouvement);
      }
      else {
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

  updateModeSortie = function(select) {
    var selected = select.options[select.selectedIndex];
    var form = select.form;
    $V(form.elements.mode_sortie, selected.get("mode"));
    form.elements._date_deces.removeClassName("notNull");
    form.elements._date_deces_da.removeClassName("notNull");
    if ($V(form.elements.mode_sortie) == "deces") {
      form.elements._date_deces.addClassName("notNull");
      form.elements._date_deces_da.addClassName("notNull");
    }
  }

  savePrefAndReload = function(prestation_id) {
    var form = getForm("editPrefPresta");
    $V(form.elements["pref[prestation_id_hospi]"], prestation_id);
    return onSubmitFormAjax(form, function() {
      getForm("typeVue").submit();
    });
  }
</script>

<!-- Formulaire de sauvegarde de l'axe de prestation en préférence utilisateur -->
<form name="editPrefPresta" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="pref[prestation_id_hospi]" />
</form>

<table class="main">
  <tr>
    <td>
      <form name="typeVue" action="?" method="get">
        <input type="hidden" name="m" value="hospi" />
        <input type="hidden" name="tab" value="edit_sorties" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        <input type="hidden" name="mode" value="{{$mode}}" onchange="refreshList(null, null, 'presents')" />
        <input type="hidden" name="hour_instantane" value="{{$hour_instantane}}" onchange="refreshList(null, null, 'presents')" />
        <select name="type_hospi" style="width: 13em;" onchange="this.form.submit()">
          <option value="">&mdash; Type d'hospitalisation</option>
          {{foreach from=$mouvements item=_mouvement key=type}}
          <option value="{{$type}}" {{if $type == $type_hospi}}selected{{/if}}>
            {{tr}}CSejour._type_admission.{{$type}}{{/tr}}
          </option>
          {{/foreach}}
        </select>
        <select name="praticien_id" style="width: 13em;" onchange="this.form.submit()">
          <option value="">&mdash; Praticien</option>
          {{foreach from=$praticiens item=_prat}}
          <option value="{{$_prat->_id}}" {{if $_prat->_id == $praticien_id}}selected{{/if}}>{{$_prat}}</option>
          {{/foreach}}
        </select>
        <select name="vue" style=" width: 12em;" onchange="this.form.submit()">
          <option value="0" {{if $vue == 0}}selected{{/if}}>Afficher les validés</option>
          <option value="1" {{if $vue == 1}}selected{{/if}}>Ne pas afficher les validés</option>
        </select>
        {{if "dPhospi prestations systeme_prestations"|conf:"CGroups-$g" == "expert"}}
          &mdash;

          Axe de prestation :
          <select name="prestation_id" onchange="savePrefAndReload(this.value);">
            <option value="">&mdash; {{tr}}None{{/tr}}</option>
            <option value="all" {{if $prestation_id == "all"}}selected{{/if}}>{{tr}}All{{/tr}}</option>
            {{foreach from=$prestations_journalieres item=_prestation}}
              <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected{{/if}}>{{$_prestation->nom}}</option>
            {{/foreach}}
          </select>
        {{/if}}
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

{{foreach from=$mouvements item=_mouvement key=type}}
  {{foreach from=$_mouvement item=_liste key=type_mouvement}}
    {{if $_liste}}
      <div id="{{$type}}_{{$type_mouvement}}" style="display: none;"></div>
    {{/if}}
  {{/foreach}}
{{/foreach}}

<div id="deplacements_" style="display: none;"></div>

{{if $type != "ambu"}}
  <div id="presents_" style="display: none;"></div>
{{/if}}
