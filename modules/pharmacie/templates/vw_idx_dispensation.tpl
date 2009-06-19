{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function refreshLists(code_cis) {
  var form = getForm("filter");
  var transmissionTab = $('li-transmissions');
  var showTransmissions = false;
  
  url = new Url;
  if (form.patient_id && $V(form.patient_id)) {
    url.setModuleAction("pharmacie", "httpreq_vw_dispensations_nominative_list");
    $('list-dispensations-title').update('Nominatif');
    $('list-stocks-title').update('Nominatif hors prescription');
    showTransmissions = true;
  }
  else {
    url.setModuleAction("pharmacie", "httpreq_vw_dispensations_list");
    $('list-dispensations-title').update('Nominatif reglobalisé');
    $('list-stocks-title').update('Global hors prescription');
  }

  transmissionTab.setVisible(showTransmissions);

  $('list-transmissions').setVisible(showTransmissions && tabs.activeContainer.id == 'list-transmissions');
  if (!showTransmissions && tabs.activeContainer.id == 'list-transmissions') {
    tabs.setActiveTab("list-dispensations");
  }
  
  $$('a[href=#list-dispensations] small').first().update('(0)');
  
  $A(form.elements).each (function (e) {
    url.addParam(e.name, $V(e));
  });
    
  if(code_cis){
    url.addParam("_selected_cis", code_cis);
    url.requestUpdate("dispensation_line_"+code_cis, { waitingText: null } );    
  } else {
    url.requestUpdate("list-dispensations", { waitingText: null } );
  }
  refreshStocks.defer();
  return false;
}

function refreshStocks() {
  var formFilter = getForm("filter");
  url = new Url;
  url.setModuleAction("pharmacie", "httpreq_vw_stocks_service_list");
  url.addParam("service_id", $V(formFilter.service_id));
  url.requestUpdate("list-stocks", { waitingText: null } );
}

function dispenseAll() {
  var submitted = false;
  $("list-dispensations").select("form").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && (f.patient_id && $V(f.patient_id) || f.service_id && $V(f.service_id)) && $V(f.date_dispensation) == 'now' && parseInt($V(f.quantity)) > 0) {
      onSubmitFormAjax(f);
      submitted = true;
    }
  });
  if (submitted) refreshLists.defer();
}

var oFormDispensation;

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
  Element.cleanWhitespace(selected);
  var dn = selected.childElements();
  if (dn[1]) {
    $V(oFormDispensation._code, dn[0].innerHTML);
    $("produit_view").update(dn[3].innerHTML.strip());
    $V(oFormDispensation.produit, "");
  }
}


</script>

{{include file=inc_filter_delivrances.tpl}}

<ul id="tab_delivrances" class="control_tabs">
  <li><a href="#list-dispensations"><span id="list-dispensations-title">Nominatif reglobalisé</span> <small>(0)</small></a></li>
  <li><a href="#list-stocks" id="list-stocks-title">Global</a></li>
  <li id="li-transmissions"><a href="#list-transmissions">Transmissions</a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-dispensations" style="display: none;"></div>
<div id="list-stocks" style="display: none;"></div>
<div id="list-transmissions" style="display:none"></div>