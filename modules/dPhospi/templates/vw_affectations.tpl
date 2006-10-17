<script language="JavaScript" type="text/javascript">
function flipChambre(chambre_id) {
  Element.classNames("chambre" + chambre_id).flip("chambrecollapse", "chambreexpand");
}

function flipSejour(sejour_id) {
  Element.classNames("sejour" + sejour_id).flip("sejourcollapse", "sejourexpand");
}

var selected_hospitalisation = null;

function selectHospitalisation(sejour_id) {
  var element = document.getElementById("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }

  selected_hospitalisation = sejour_id;
 
  submitAffectation();
}

var selected_lit = null;

function selectLit(lit_id) {
  var element = document.getElementById("lit" + selected_lit);
  if (element) {
    element.checked = false;
  }

  selected_lit = lit_id;
  
  submitAffectation();
}

function submitAffectation() {
  if (selected_lit && selected_hospitalisation) {
    var form = eval("document.addAffectationsejour" + selected_hospitalisation);
    form.lit_id.value = selected_lit;
    form.submit();
  }
}

function DragDropSejour(sejour_id, lit_id){
  var form = eval("document.addAffectation" + sejour_id);
  $(sejour_id).style.display="none";
  form.lit_id.value = lit_id;
  form.submit();
}

function submitAffectationSplit(form) {
  form._new_lit_id.value = selected_lit;
  if (!selected_lit) {
    alert("Veuillez sélectionner un nouveau lit et revalider la date");
    return;
  }
  
  if (form._date_split.value <= form.entree.value || 
      form._date_split.value >= form.sortie.value) {
    var msg = "La date de déplacement (" + form._date_split.value + ") doit être comprise entre";
    msg += "\n- la date d'entrée: " + form.entree.value; 
    msg += "\n- la date de sortie: " + form.sortie.value;
    alert(msg);
    return;
  }
  
  form.submit();
}

function setupCalendar(affectation_id) {
  var form = null;
  
  if (form = eval("document.entreeAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_entree",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_entree",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.entreeAffectation" + affectation_id);
            form.submit();
          }
        }
      }
    );
  }

  
  if (form = eval("document.sortieAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_sortie",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_sortie",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.sortieAffectation" + affectation_id);
            form.submit();
          }
        }
      }
    );
  }
  
  if (form = eval("document.splitAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "__date_split",
        ifFormat    : "%Y-%m-%d %H:%M:%S",
        button      : form.name + "__trigger_split",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.splitAffectation" + affectation_id);
            submitAffectationSplit(form);
          }
        }
      }
    );
  }

}

function popPlanning() {
  url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
}

function showLegend() {
  url = new Url;
  url.setModuleAction("dPhospi", "legende");
  url.popup(500, 500, "Legend");
}

function showAlerte() {
  url = new Url;
  url.setModuleAction("dPhospi", "vw_etat_semaine");
  url.popup(500, 250, "Alerte");
}

var serviceToReload = 0;

function chgVwService(oElement) {
  var oForm          = document.listServiceVw;
  serviceToReload = oElement.value;
  var sTargetName = "vwService[" + serviceToReload + "]";
  var oTargetElement = oForm.elements[sTargetName];
  if(oElement.checked) {
    oTargetElement.value="1";
  } else {
    oTargetElement.value="0";
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete : reloadService });
}

function reloadService() {
  var url = new Url;
  url.setModuleAction("dPhospi", "httpreq_vw_aff_service");
  url.addParam("service_id", serviceToReload);
  url.addParam("mode", {{$mode}});
  url.requestUpdate("service" + serviceToReload);
}

function pageMain() {
  regRedirectFlatCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
}

</script>

<table class="main">

  {{if $alerte}}
  <tr>
    <td colspan="3">
      <div class="warning">
        <a href="javascript:showAlerte()">Il y a {{$alerte}} patient(s) à placer dans la semaine qui vient</a>
      </div>
    </td>
  </tr>
  {{/if}}

  <tr>
    <td>
      <a href="javascript:showLegend()" class="buttonsearch">Légende</a>
    </td>
    <th>
      Planning du {{$date|date_format:"%A %d %B %Y"}} : {{$totalLits}} place(s) de libre
    </th>
    <td>
      <form name="chgMode" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="mode" title="Veuillez choisir un type de vue">Type de vue</label>
      <select name="mode" onchange="submit()">
        <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>Vue instantanée</option>
        <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>Vue de la journée</option>
      </select>
      </form>
    </td>
  </tr>

  <tr>
    <td class="greedyPane" colspan="2">
      <form name="listServiceVw" action="?m={{$m}}" method="post" onsubmit="return checkForm(this);">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_services_vw_modify" />
        {{foreach from=$vwService key=key_vw item=curr_vw}}
        <input type="hidden" name="vwService[{{$key_vw}}]" value="{{$curr_vw}}" />
        {{/foreach}}
      </form>
      <table class="tbl">
        <tr>
        {{foreach from=$services item=curr_service}}
          <th>
            <input type="checkbox" name="service{{$curr_service->service_id}}" value="{{$curr_service->service_id}}" style="float:right"
            onchange="chgVwService(this);" {{if $curr_service->_vwService}}checked="checked"{{/if}}/>
            {{$curr_service->nom}}
            {{if $curr_service->_vwService}}
            / {{$curr_service->_nb_lits_dispo}} lit(s) dispo
            {{/if}}
          </th>
        {{/foreach}}
        </tr>
        <tr>
        {{foreach from=$services item=curr_service}}
          <td id="service{{$curr_service->service_id}}">
          {{include file="inc_affectations_services.tpl"}}
          </td>
        {{/foreach}}
        </tr>
      </table>
    </td>
    <td class="pane">
      <div id="calendar-container"></div>
      {{if $canEdit}}
      {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
        {{include file="inc_affectations_liste.tpl"}}
      {{/foreach}}
      {{/if}}
    </td>
  </tr>

</table>