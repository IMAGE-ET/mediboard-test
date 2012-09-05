{{* $Id: vw_placement_patients.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
var element_select;
var zone_select;

ChoiceLit  = {
  modal: null,
  edit: function(chambre_id, patient_id, date) {  
    var url = new Url("dPhospi", "ajax_choice_lit");
    url.addParam("chambre_id", chambre_id);
    url.addParam("patient_id", patient_id);
    url.addParam("date", date);
    url.addParam("vue_hospi", true);
    url.requestModal(); 
    this.modal = url.modalObject;
  },
  finish: function(lit_id, modal_ouvert) {
    if(modal_ouvert){
      ChoiceLit.modal.close();
    }
    if (element_select.id == "lit_bloque") {
      editAffectation(null, lit_id, 0);
    }
    else {
      if (element_select.id == "lit_urgence") {
        editAffectation(null, lit_id, 1);
      }
      else {
        zone_select.appendChild(element_select);
        var guid = element_select.get("affectation-guid");
        var form = getForm(guid);
        if (!form.affectation_id.value) {
          form.lit_id.value = lit_id;
          var a = onSubmitFormAjax(form);
          refreshNonPlaces();
          refreshService(zone_select.get("service-id"));
          return a;
        }
        else {
          return moveAffectation(form.affectation_id.value, lit_id, form.sejour_id.value, form.lit_id.value, zone_select.get("service-id"));
        }
      }
    }
  },

  savePlan: function(element, zoneDrop){
    element_select = element;
    zone_select = zoneDrop;
    element.style.width="95%";

    var nb_chambres_libres = parseInt(zoneDrop.getAttribute("data-nb-lits")) - parseInt(zoneDrop.select('div.patient').length);
    if(nb_chambres_libres >= 2){
      ChoiceLit.edit(zoneDrop.get("chambre-id"), element.get("patient-id"), getForm("changeDatee").date.value);    
    }
    else if(nb_chambres_libres == 1){
      return ChoiceLit.finish(zoneDrop.get("lit-id"));
    }
  }
};
refreshMouvements = function(after_refresh, lit_id) {
  if (after_refresh) {
    Control.Modal.close();
  }
  refreshService(zone_select.get("service-id"));
}
editAffectation = function(affectation_id, lit_id, urgence) {
  var url = new Url("dPhospi", "ajax_edit_affectation");
  url.addParam("affectation_id", affectation_id);
  
  if (!Object.isUndefined(lit_id)) {
    url.addParam("lit_id", lit_id);
  }
  if (!Object.isUndefined(urgence)) {
    url.addParam("urgence", urgence);
  }
  
  Placement.stop();
  var modal = url.requestModal(500, null, {showReload: false});
  modal.modalObject.observe("afterClose", function() { Placement.loadTopologique(); });
}

delAffectation = function(affectation_id, lit_id, sejour_guid) {
  var form = getForm("delAffect");
  $V(form.affectation_id, affectation_id);
  
  return onSubmitFormAjax(form, {onComplete: function(){
    refreshNonPlaces();
    var service = $('affectation_topologique_'+affectation_id).parentNode;
    refreshService(service.get("service-id"));
  }});
}

moveAffectation = function(affectation_id, lit_id, sejour_id, lit_id_origine, service_id) {
  var url = new Url("dPhospi", "ajax_move_affectation");
  if (!Object.isUndefined(affectation_id)) {
    url.addParam("affectation_id", affectation_id);
  }
  url.addParam("lit_id", lit_id);
  
  if (!Object.isUndefined(sejour_id)) {
    url.addParam("sejour_id", sejour_id);
  }
  return url.requestUpdate("systemMsg", {onComplete: function() {
    if(!lit_id_origine || !lit_id){
      refreshNonPlaces();
    }
    if(service_id){
      refreshService(service_id);
    }
  }});
}

Main.add(function() {
  Calendar.regField(getForm("changeDatee").date, null, { noView: true } );
  Rafraichissement.start(300);//rafraichissement toutes les 5 minutes
} );

refreshTopologie = function(date){
  var url = new Url('dPhospi', 'vw_placement_patients');
  url.addParam("date", date.value);
  url.requestUpdate('topologique');
}

refreshService = function(service_id){
  var url = new Url('dPhospi', 'ajax_refresh_service');
  url.addParam("date", getForm("changeDatee").date.value);
  url.addParam("service_id", service_id);      
  url.requestUpdate('service-'+service_id); 
}

refreshNonPlaces = function(){
  var url = new Url('dPhospi', 'ajax_refresh_patients_non_places');
  url.addParam("date", getForm("changeDatee").date.value);
  url.requestUpdate('patients_non_places'); 
}

Rafraichissement = {  
  init: function() {
    refreshTopologie(getForm("changeDatee").date);
  },
  
  start: function(delay) {
    this.init.delay(delay);
  }
}
</script>

<style>
div.patient{
  margin: 3px;
  background-color: rgba(255,255,255,0.8);
  border:1px solid silver;
  width: 95%;
  min-height:35px;
}

div.list-patients-non-places{
  min-width:120px;
 }

div.grille{
  width:100%;
  float:left;
  margin-left:5px;
  margin-right:5px;
 }

div.grille table{
  border-spacing: 9px;
  border-collapse:separate;
}

div.grille td.chambre{
  vertical-align: top;
  white-space :normal;
  width:120px;
  height:80px;
}

div.grille small{
  float: right;
  margin-top: -11px;
  background: #ABE;
  border-radius: 2px;
  padding: 0 3px;
  text-shadow:  0 0 0 transparent,
            -1px  0  .0px rgba(255,255,255,.7), 
             0   1px .0px rgba(255,255,255,.7), 
             1px  0  .0px rgba(255,255,255,.7), 
             0  -1px .0px rgba(255,255,255,.7);
}

div.ssr-sejour-bar {
  float:right;
  position:relative;
}
.toolbar_affectation_topo{
  float:right;
  visibility:visible;
}

  table.table_grille {
    table-layout:fixed; 
  }
</style>
<div style="text-align:center;">
  <strong><big>{{$date|date_format:$conf.longdate}}</big></strong>
  <form action="" name="changeDatee" method="get" onsubmit="return refreshTopologie(this.date);">
    <input type="hidden" name="date" class="date" value="{{$date}}" onchange="refreshTopologie(this);" />
  </form>
</div>
{{if count($services)}}
<table class="main">
  <tr>
    <td>
      <table id="patients_non_places">
        {{mb_include module=hospi template=inc_patients_non_places}}
      </table>
    </td>
    <td style="width:100%;">
      <table style="width:100%;">
        {{foreach from=$grilles item=grille key=key}}
          <tr><th class="title">{{$services[$key]}}</th></tr>
          <tr>
            <td>
              <div class="grille">
                <table class="main table_grille" id="service-{{$key}}">
                  {{mb_include module=hospi template=inc_plan_service}}
                </table>
              </div>
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>

<!-- Formulaire de suppression d'affectation -->
<form name="delAffect" method="post" action="?">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="affectation_id" value="" />
</form>
{{else}}
<div class="big-info">
  {{tr}}CAffectation-choose_services{{/tr}}
</div>
{{/if}}