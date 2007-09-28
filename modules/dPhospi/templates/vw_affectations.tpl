<script language="JavaScript" type="text/javascript">
function flipChambre(chambre_id) {
  Element.classNames("chambre-" + chambre_id).flip("chambrecollapse", "chambreexpand");
}

function flipSejour(sejour_id) {
  Element.classNames("sejour_" + sejour_id).flip("sejourcollapse", "sejourexpand");
}

var selected_hospitalisation = null;
var selected_hospi = false;
function selectHospitalisation(sejour_id) {
  var element = document.getElementById("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }
  selected_hospitalisation = sejour_id;
  selected_hospi = true;
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
  if (selected_lit && selected_hospi) {
    if(selected_hospitalisation){
      var form = eval("document.addAffectationsejour_" + selected_hospitalisation);
    }else{
      var form = eval("document.addAffectationsejour");    
    }
    form.lit_id.value = selected_lit;
    form.submit();
  }
}

function DragDropSejour(sejour_id, lit_id){
  var oForm = eval("document.addAffectation" + sejour_id);
  $(sejour_id).style.display="none";
  oForm.lit_id.value = lit_id;
  oForm.submit();
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
            var form = eval("document.entreeAffectation" + affectation_id);
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
            var form = eval("document.sortieAffectation" + affectation_id);
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
            var form = eval("document.splitAffectation" + affectation_id);
            submitAffectationSplit(form);
          }
        }
      }
    );
  }

}

function popPlanning() {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
}

function showRapport() {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_rapport");
  url.addParam("date","{{$date}}")
  url.popup(800, 600, "Rapport");
}

function showLegend() {
  var url = new Url;
  url.setModuleAction("dPhospi", "legende");
  url.popup(500, 500, "Legend");
}

function showAlerte() {
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_etat_semaine");
  url.popup(500, 250, "Alerte");
}

function reloadService(oElement) {
  if (oElement.checked) {
    var idService = oElement.value;
    var url = new Url;
    url.setModuleAction("dPhospi", "httpreq_vw_aff_service");
    url.addParam("service_id", idService);
    url.addParam("mode", {{$mode}});
    url.requestUpdate("service" + idService);
  }
}

function viewPrevTimeHospi(affectation_id, chir_id, codes) {
  var oElement = $("tpsPrev"+affectation_id);
  oElement.show();
  if(oElement.alt != "infos - cliquez pour fermer") {
    var url = new Url;
    url.setModuleAction("dPplanningOp", "httpreq_get_hospi_time");
    url.addParam("chir_id", chir_id);
    url.addParam("codes", codes);
    url.addParam("javascript", 0);
    url.requestUpdate(oElement);
    oElement.alt = "infos - cliquez pour fermer";
  }
}

function hidePrevTimeHospi(affectation_id) {
  var oElement = $("tpsPrev"+affectation_id);
  oElement.hide();
}

function pageMain() {
  // PairEffect.InitGroup can't be used because it scans all DOM nodes
  {{foreach from=$services item=curr_service}}
  new PairEffect("service{{$curr_service->service_id}}", {
    bStartVisible: true,
    sEffect: "appear",
    sCookieName: "fullService"
  } );
  {{/foreach}}

  regRedirectFlatCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
  
 
  regFieldCalendar("addAffectationsejour", "entree", true);
  regFieldCalendar("addAffectationsejour", "sortie", true);

}

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <div style="float:right;">
        <strong>Planning du {{$date|date_format:"%A %d %B %Y"}}</strong>
      </div>
      {{if $alerte}}
      <div class="warning">
        <a href="#" onclick="showAlerte()">Il y a {{$alerte}} patient(s) à placer dans la semaine qui vient</a>
      </div>
      {{else}}
      <div class="message">
        Tous les patients sont placés pour la semaine à venir
      </div>
      {{/if}}
    </td>
    <th>
      {{$totalLits}} place(s) de libre
    </th>
  </tr>

  <tr>
    <td>
      <a href="#" onclick="showLegend()" class="buttonsearch">Légende</a>
      <a href="#" onclick="showRapport()" class="buttonprint">Rapport</a>
    </td>
    
    <td>
      <form name="chgAff" action="?m={{$m}}" method="get">
      {{foreach from=$services item=curr_service}}
        <input
          type="checkbox"
          name="service{{$curr_service->service_id}}"
          id="service{{$curr_service->service_id}}-trigger"
          value="{{$curr_service->service_id}}"
          onchange="reloadService(this);"
          {{if $curr_service->_vwService}}checked="checked"{{/if}}
        />
        <label for="service{{$curr_service->service_id}}" title="Afficher le service {{$curr_service->nom}}">
          {{$curr_service->nom}}
        </label>
      {{/foreach}}
      </form>
    </td>
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
      <table class="affectations">
        <tr>
        {{foreach from=$services item=curr_service}}
          <td class="fullService" id="service{{$curr_service->service_id}}">
          {{include file="inc_affectations_services.tpl"}}
          </td>
        {{/foreach}}
        </tr>
      </table>
    </td>
    <td class="pane">
      <div id="calendar-container"></div>
      {{if $can->edit}}
      
      <table class="form">
        <tr>
          <td class="button">
            <form name="chgFilter" action="?m={{$m}}" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <label for="filterAdm" title="Admissions à afficher">Admissions</label>
              <select name="filterAdm" onchange="submit()">
                <option value="tout" {{if $filterAdm == 0}}selected="selected"{{/if}}>&mdash; Tout afficher</option>
                <option value="ambu" {{if $filterAdm == "ambu"}}selected="selected"{{/if}}>Ambulatoires</option>
                <option value="comp" {{if $filterAdm == "comp"}}selected="selected"{{/if}}>Hospi. complètes</option>
                <option value="csejour" {{if $filterAdm == "csejour"}}selected="selected"{{/if}}>Courts séjours</option>
              </select>
            </form>
          </td>
        </tr>
        <tr>
          <td class="button">
            <form name="chgFilter_" action="?m={{$m}}" method="get">
              <input type="hidden" name="m" value="{{$m}}" />
              <label for="triAdm" title="Admissions à afficher">Trier par</label>
              <select name="triAdm" onchange="submit()">
                <option value="praticien" {{if $triAdm == "praticien"}}selected="selected"{{/if}}>Praticien</option>
                <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Heure d'entrée</option>
              </select>
            </form>
          </td>
        </tr>
      </table>
      
      <form name="addAffectationsejour" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="lit_id" value="" />
      <input type="hidden" name="sejour_id" value="" />
            
      <table class="sejourcollapse" id="sejour_bloque">
        <tr>
        <td class="selectsejour">
          <input type="radio" id="hospitalisation" onclick="selectHospitalisation()" />
          <script type="text/javascript">new Draggable('sejour_bloque', {revert:true})</script>
        </td>
        <td class="patient" onclick="flipSejour('bloque')">
          <strong><a name="sejourbloque">[BLOQUER UN LIT]</a></strong>
        </td>
        </tr>
        <tr>
          <td class="date"><em>Entrée</em></td>
          <td class="date">{{mb_field object=$affectation field="entree" form="addAffectationsejour" }}</td>
        </tr>
        <tr>
          <td class="date"><em>Sortie</em></td>
          <td class="date">{{mb_field object=$affectation field="sortie" form="addAffectationsejour" }}</td>
      </tr>
      <tr>
        <td class="date highlight" colspan="2">
          <label for="rques">Remarques</label> : 
          <textarea name="rques"></textarea>
        </td>
      </tr>
      </table>
      </form>
      
      {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
        {{include file="inc_affectations_liste.tpl"}}
      {{/foreach}}
      {{/if}}
    </td>
  </tr>

</table>