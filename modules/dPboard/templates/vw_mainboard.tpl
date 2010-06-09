{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPcompteRendu" script="document"}}
{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">
function viewItem(oTd, guid, date) {
  oTd = $(oTd);
  
  // Mise en surbrillance de la plage survolée
  $$('td.selectedConsult').each(function(elem) { elem.className = "nonEmptyConsult";});
  $$('td.selectedOp').each(function(elem) { elem.className = "nonEmptyOp";});
  
  var parts = guid.split('-'),
      sClassName = parts[0],
      id = parts[1];
      
  if(sClassName == "CPlageconsult"){
    oTd.up().className = "selectedConsult";
  }else if(sClassName == "CPlageOp"){
    oTd.up().className = "selectedOp";
  }
  
  // Affichage de la plage selectionnée et chargement si besoin
  Dom.cleanWhitespace($('viewTooltip'));
  $('viewTooltip').childElements().invoke('hide');

  var oElement = $(guid).show();
  
  if(oElement.alt == "infos - cliquez pour fermer") {
    return;
  }
  
  var url = new Url;
  url.addParam("board"     , "1");
  url.addParam("boardItem" , "1");
  
  if(sClassName == "CPlageconsult"){
    url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("plageconsult_id", id);
    url.addParam("date"           , date);
    url.addParam("chirSel"        , "{{$pratSel->_id}}");
    url.addParam("vue2"           , "{{$vue}}");
    url.addParam("selConsult"     , "");
  } else if(sClassName == "CPlageOp"){
    url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");
    url.addParam("chirSel" , "{{$pratSel->_id}}");
    url.addParam("date"    , date);
    url.addParam("urgences", "0");
  } else return;
  
  url.requestUpdate(oElement);
  oElement.alt = "infos - cliquez pour fermer";
}

function affNaissance() {
  var oForm      = document.find;
  var oCheckNaissance = oForm.check_naissance;
  var oNaissance = oForm.naissance;
  var oDay       = oForm.Date_Day;
  var oMonth     = oForm.Date_Month;
  var oYear      = oForm.Date_Year;
  if (oCheckNaissance.checked) {
    oDay.style.display   = "inline";
    oMonth.style.display = "inline";
    oYear.style.display  = "inline";
    oNaissance.value     = "on";
  } else {
    oDay.style.display   = "none";
    oMonth.style.display = "none";
    oYear.style.display  = "none";
    oNaissance.value     = "off";
  }
}

function hideIcon(frame) {
  $("icon-" + frame).hide();
}

function showIcon(frame) {
  $("icon-" + frame).show();
}

function updateListConsults() {
  var url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_list_consult");

  url.addParam("chirSel"   , "{{$pratSel->_id}}");
  url.addParam("date"      , "{{$date}}");
  url.addParam("vue2"      , "{{$vue}}");
  url.addParam("selConsult", "");
  url.addParam("board"     , "1");

  url.requestUpdate("consultations");
}

function updateListOperations() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "httpreq_vw_list_operations");

  url.addParam("chirSel" , "{{$pratSel->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("urgences", "0");
  url.addParam("board"   , "1");

  url.requestUpdate("operations");
}

function updateListPatients() {
  var url = new Url("dPpatients", "httpreq_list_patients");
  
  var oForm = getForm("find");
  if(oForm) {
    url.addElement(oForm.nom);
    url.addElement(oForm.prenom);
    url.addElement(oForm.naissance);
    url.addElement(oForm.Date_Day);
    url.addElement(oForm.Date_Month);
    url.addElement(oForm.Date_Year);
    url.addElement(oForm.patient_ipp);
  }
  url.addParam("board"   , 1);

  url.requestUpdate("patients");
  return false;
}

function updateListHospi() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_vw_hospi");

  url.addParam("chirSel" , "{{$pratSel->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("hospi");
}

function updateSemainier() {
  var url = new Url;
  url.setModuleAction("dPboard", "httpreq_semainier");

  url.addParam("chirSel" , "{{$pratSel->_id}}");
  url.addParam("date"    , "{{$date}}");
  url.addParam("board"   , "1");

  url.requestUpdate("semainier");
}

Main.add(function () {
  {{if $view == "day"}}
    hideIcon("consultations");
    hideIcon("operations");
    hideIcon("hospi");
    hideIcon("patients");
    {{if $prat}}
    updateListConsults();
    updateListOperations();
    updateListPatients();
    updateListHospi();
    {{/if}}
    ViewPort.SetAvlHeight("consultations", 0.5);
    ViewPort.SetAvlHeight("operations", 0.5);
  	ViewPort.SetAvlHeight("patients", 1);
  	ViewPort.SetAvlHeight("hospi", 1);
  {{/if}}
  {{if $view == "week"}}
    updateSemainier();
  {{/if}}
  
  Calendar.regField(getForm("changeDate").date, null, {noView: true});
});

</script>

<!-- Script won't be evaled in Ajax inclusion. Need to force it -->
{{mb_include_script script=intermax}}

<table class="main">
  {{if $secretaire || $admin}}
  <tr>
    <form name="praticien" method="post">
	    <select name="praticien_id" onchange="form.submit()">
	    <option value="">&mdash; Choix d'un praticien</option>
	    {{foreach from=$listPraticiens item="praticien"}}
	      <option value="{{$praticien->_id}}" class="mediuser" style="border-color: #{{$praticien->_ref_function->color}};" {{if $praticien_id == $praticien->_id}}selected = "selected"{{/if}}>{{$praticien->_view}}</option>
	    {{/foreach}}
	    </select>
	  </form>
  </tr>
  {{/if}}
  <tr>
    <th class="halfPane">
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$prec}}">&lt;&lt;&lt;</a>
      <form name="changeDate" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        {{$date|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
    <th class="halfPane">
      <form name="editFrmView" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="radio" name="view" value="day" {{if $view == "day"}}checked="checked"{{/if}} onclick="this.form.submit()" />
      <label for="view_day" title="Affichage du jour">Journée</label>
      <input type="radio" name="view" value="week" {{if $view == "week"}}checked="checked"{{/if}} onclick="this.form.submit()" />
      <label for="view_week" title="Affichage de la semaine">Semainier</label>
      </form>
    </th>
  </tr>
  
  {{if $view == "day"}}
  <tbody class="viewported">
  <tr>
    <!--  Consultations -->
    <td class="viewport" onmouseover="showIcon('consultations')" onmouseout="hideIcon('consultations')">
      <div style="position:absolute" id="icon-consultations">
        <a href="?m=dPcabinet&amp;tab=edit_consultation&amp;date={{$date}}">
          <img src="modules/dPcabinet/images/icon.png" height="24" width="24" />
        </a>
      </div>
      <div id="consultations">
      </div>
    </td>
    
    <!-- Operations -->
    <td class="viewport" onmouseover="showIcon('operations')" onmouseout="hideIcon('operations')">
      <div style="position:absolute" id="icon-operations">
        <a href="?m=dPplanningOp&amp;tab=vw_idx_patients">
          <img src="modules/dPplanningOp/images/icon.png" height="24" width="24" />
        </a>
      </div>
      <div id="operations">
      </div>
    </td>
  </tr>
  
  <tr>
    <!-- Recherche de patients -->
    <td class="viewport" id="patients-viewport" onmouseover="showIcon('patients')" onmouseout="hideIcon('patients')">
      <div style="position:absolute" id="icon-patients">
        <a href="?m=dPpatients&amp;tab=vw_idx_planning&amp;date={{$date}}">
          <img src="modules/dPpatients/images/icon.png" height="24" width="24" />
        </a>
      </div>
      <div id="patients" style="overflow: auto">
      </div>
    </td>

    <!-- Patients hospitalisés -->
    <td class="viewport" onmouseover="showIcon('hospi')" onmouseout="hideIcon('hospi')">
      <div style="position:absolute" id="icon-hospi">
        <img src="modules/dPhospi/images/icon.png" height="24" width="24" />
      </div>
      <div id="hospi" style="overflow: auto">
      </div>
    </td>
  </tr>
  </tbody>
  {{/if}}
  
  {{if $view == "week"}}
  <tr>
    <td id="semainier" style="border: 1px dotted #000;" colspan="2"></td>
  </tr>
  {{/if}}
</table>