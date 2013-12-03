{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="dPpatients" script="patient"}}
{{mb_script module="soins" script="plan_soins"}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="element_selector"}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}

<style type="text/css">

.modal div{
  text-align: left;
}

</style>

<script type="text/javascript">

Main.add(function () {
  var tab_sejour = Control.Tabs.create('tab-pancarte', false);
  viewTransmissions($V(document.selService.service_id), null, null, '1', '1');
  viewPancarte();
});

viewPancarte = function() {
  var oForm = getForm("selService");
  var url = new Url("soins", "ajax_vw_pancarte");
  url.addParam("service_id", $V(oForm.service_id));
  url.addParam("debut", $V(oForm.debut));
  {{if $conf.soins.soin_refresh_pancarte_service != 'none'}}
    url.periodicalUpdate("pancarte_service", { frequency: {{math equation="a*60" a=$conf.soins.soin_refresh_pancarte_service}} });
  {{else}}
    url.requestUpdate("pancarte_service");
  {{/if}}
};

function viewLegendPancarte(){
  var url = new Url("soins", "vw_legende_pancarte");
  url.popup(300, 500, "Légende de la pancarte");
}

function viewTransmissions(service_id, user_id, degre, observations, transmissions, refresh, order_col, order_way){
  var url = new Url("soins", "httpreq_vw_transmissions_pancarte");
  url.addParam("service_id", service_id);
  url.addParam("user_id", user_id);
  url.addParam("degre", degre);
  url.addParam("date", "{{$date}}");
  url.addParam("date_min", "{{$date_min}}");
  url.addParam("observations", observations?1:0);
  url.addParam("transmissions", transmissions?1:0);
  url.addParam("refresh", refresh);
  if(order_col && order_way){
    url.addParam("order_col", order_col);
    url.addParam("order_way", order_way);
  }
  if(user_id || degre || refresh){
    url.requestUpdate("_transmissions");
  } else {
    url.requestUpdate("viewTrans");
  }
}

</script>

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
{{/if}}

<form name="viewSoin" method="get" action="?">
  <input type="hidden" name="m" value="soins" />
  <input type="hidden" name="tab" value="vw_idx_sejour" />
  <input type="hidden" name="sejour_id" value="" />
  <input type="hidden" name="date" value="{{$date}}" />
  <input type="hidden" name="mode" value="1" />
  <input type="hidden" name="_active_tab" value="dossier_soins" />
</form>

<ul id="tab-pancarte" class="control_tabs">
  <li><a href="#pancarte_service">Pancarte {{$service->_view}}</a></li>
  <li><a href="#viewTrans">Transmissions</a></li>
  <li style="margin-top: 2px; padding-left: 2em; vertical-align: top;">
    <form name="selService" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="vw_pancarte_service" />
      <select name="service_id" onchange="this.form.submit();">
        <option value="">&mdash; Choix d'un service</option>
        {{foreach from=$services item=_service}}
          <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
        {{/foreach}}
      </select>
      le
      {{mb_field object=$filter_line field="debut" register=true form=selService onchange="this.form.submit();" class="notNull"}}
    </form>
  </li>
  <li style="float: right;">
    <button type="button" class="search" onclick="viewLegendPancarte();">Légende</button>
  </li>
</ul>
<hr class="control_tabs" />

{{assign var=images value="CPrescription"|static:"images"}}

<div id="pancarte_service" style="display: none;"></div>
<div id="viewTrans" style="display: none;"></div>
