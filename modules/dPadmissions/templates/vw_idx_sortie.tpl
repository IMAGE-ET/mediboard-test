{{* $Id: *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=admissions script=admissions}}
{{mb_script module=compteRendu script=document}}
{{mb_script module=compteRendu script=modele_selector}}
{{mb_script module=cabinet script=file}}
{{if "web100T"|module_active}}
  {{mb_script module=web100T script=web100T}}
{{/if}}

<script type="text/javascript">

var sejours_enfants_ids;

function printAmbu(type){
  var url = new Url("dPadmissions", "print_ambu");
  url.addParam("date", "{{$date}}");
  url.addParam("type", type);
  url.popup(800,600,"Ambu");
}

function printPlanning() {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "print_sorties");
  url.addParam("date"       , "{{$date}}");
  url.addParam("type", $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.popup(700, 550, "Sorties");
}

function printDHE(type, object_id) {
  var url = new Url("dPplanningOp", "view_planning");
  url.addParam(type, object_id);
  url.popup(700, 550, "DHE");
}

var changeEtablissementId = function(oForm) {
  $V(oForm._modifier_sortie, '0');
  var type = $V(oForm.type);
  submitSortie(oForm, type);
};

function reloadFullSorties(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_all_sorties");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.addParam("prat_id"   , $V(oForm.prat_id));
  url.requestUpdate('allSorties');
  reloadSorties(filterFunction);
}

function reloadSorties(filterFunction) {
  var oForm = getForm("selType");
  var url = new Url("dPadmissions", "httpreq_vw_sorties");
  url.addParam("date"      , "{{$date}}");
  url.addParam("type"      , $V(oForm._type_admission));
  url.addParam("service_id", [$V(oForm.service_id)].flatten().join(","));
  url.addParam("prat_id"   , $V(oForm.prat_id));
  url.addParam("period"    , $V(oForm.period));
  
  if(!Object.isUndefined(filterFunction)){
    url.addParam("filterFunction" , filterFunction);
  }
  url.requestUpdate("listSorties");
}

function submitSortie(oForm) {

  if (!Object.isUndefined(oForm.elements["_sejours_enfants_ids"]) && $V(oForm._modifier_sortie) == 1) {
    sejours_enfants_ids = $V(oForm._sejours_enfants_ids);
    sejours_enfants_ids.split(",").each(function(elt) {
      var form = getForm("editFrmCSejour-"+elt);
      if (!Object.isUndefined(form) && form.down("button.tick")) {
        if (confirm('Voulez-vous effectuer dans un même temps la sortie de l\'enfant ' + form.get("patient_view"))) {
          form.down("button.tick").onclick();
        }
      }
    });

    sejours_enfants_ids = undefined;
    return onSubmitFormAjax(oForm, { onComplete : reloadSorties });
  }

  if (!Object.isUndefined(sejours_enfants_ids) && sejours_enfants_ids.indexOf($V(oForm.sejour_id)) != -1) {
    return onSubmitFormAjax(oForm);
  }
  else {
    return onSubmitFormAjax(oForm, { onComplete : reloadSorties });
  }
}

function confirmation(oForm, type){
   if(!checkForm(oForm)){
     return false;
   }
   if(confirm('La date enregistrée de sortie est différente de la date prévue, souhaitez vous confimer la sortie du patient ?')){
     submitSortie(oForm, type);
   }
   else {
     sejours_enfants_ids = undefined;
   }
}

function confirmation(date_actuelle, date_demain, sortie_prevue, entree_reelle, oForm){
  if(entree_reelle == ""){
    if(!confirm('Attention, ce patient ne possède pas de date d\'entrée réelle, souhaitez vous confirmer la sortie du patient ?')){
      sejours_enfants_ids = undefined;
      return false;
    }
  }
  if(date_actuelle > sortie_prevue || date_demain < sortie_prevue) {
    if(!confirm('La date enregistrée de sortie est différente de la date prévue, souhaitez vous confimer la sortie du patient ?')){
      sejours_enfants_ids = undefined;
      return false;
    }
  }
  submitSortie(oForm);
}

Main.add(function () {
  var totalUpdater = new Url("dPadmissions", "httpreq_vw_all_sorties");
  totalUpdater.addParam("date", "{{$date}}");
  Admissions.totalUpdater = totalUpdater.periodicalUpdate('allSorties', { frequency: 120 });

  var listUpdater = new Url("dPadmissions", "httpreq_vw_sorties");
  listUpdater.addParam("selSortis", "{{$selSortis}}");
  listUpdater.addParam("date", "{{$date}}");
  Admissions.listUpdater = listUpdater.periodicalUpdate('listSorties', {
    frequency: 120,
    onCreate: function() {
      WaitingMessage.cover($('listSorties'));
      Admissions.rememberSelection('listSorties');
    }
  });
});

</script>

<div style="display: none" id="area_prompt_modele">
  {{mb_include module=admissions template=inc_prompt_modele type=sortie}}
</div>

<table class="main">
<tr>
  <td>
    <a href="#legend" onclick="Admissions.showLegend()" class="button search">Légende</a>
    {{if "astreintes"|module_active}}{{mb_include module=astreintes template=inc_button_astreinte_day date=$date}}{{/if}}
  </td>
  <td style="float: right">
    <form action="?" name="selType" method="get">
      <select name="period" onchange="reloadSorties();">
        <option value=""      {{if !$period          }}selected{{/if}}>&mdash; Toute la journée</option>
        <option value="matin" {{if $period == "matin"}}selected{{/if}}>Matin</option>
        <option value="soir"  {{if $period == "soir" }}selected{{/if}}>Soir</option>
      </select>
      {{mb_field object=$sejour field="_type_admission" emptyLabel="CSejour.all" onchange="reloadFullSorties();"}}
      <select name="service_id" onchange="reloadFullSorties();" {{if $sejour->service_id|@count > 1}}size="5" multiple="true"{{/if}}>
        <option value="">&mdash; Tous les services</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if in_array($_service->_id, $sejour->service_id)}}selected="selected"{{/if}}>{{$_service}}</option>
        {{/foreach}}
      </select>
      <input type="checkbox" onclick="Admissions.toggleMultipleServices(this)" {{if $sejour->service_id|@count > 1}}checked="checked"{{/if}}/>
      <select name="prat_id" onchange="reloadFullSorties();">
        <option value="">&mdash; Tous les praticiens</option>
        {{foreach from=$prats item=_prat}}
          <option value="{{$_prat->_id}}" {{if $_prat->_id == $sejour->praticien_id}}selected="selected"{{/if}}>{{$_prat}}</option>
        {{/foreach}}
      </select>
    </form>
    <a href="#" onclick="printPlanning()" class="button print">Imprimer</a>
    <a href="#" onclick="Admissions.beforePrint(); Modal.open('area_prompt_modele')" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
    {{if "web100T"|module_active}}
      {{mb_include module=web100T template=inc_button_send_all_prestations type=sortie}}
    {{/if}}
  </td>
</tr>
  <tr>
    <td id="allSorties" style="width: 250px">
    </td>
    <td id="listSorties" style="width: 100%">
    </td>
  </tr>
</table>