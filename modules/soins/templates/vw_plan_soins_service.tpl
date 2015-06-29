{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module="soins" script="plan_soins"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
{{/if}}

<script>
  // Refresh du plan de soin
  updatePlanSoinsPatients = function(hide_old_lines) {
    var oForm = getForm("selectElts");
    if ($('content_plan_soins_service')) {
      var url = new Url("soins", "ajax_vw_content_plan_soins_service");
      url.addParam("categories_id[]", $V(getForm("selectElts").elts), true);
      url.addParam("date", "{{$date}}");
      url.addParam("premedication", $V(oForm.premedication) ? 1 : 0);
      if (!Object.isUndefined(hide_old_lines)) {
        url.addParam("hide_old_lines", hide_old_lines);
      }
      url.requestUpdate("content_plan_soins_service");
    }
  };

  addTransmission = function(sejour_id, user_id, transmission_id, object_id, object_class, libelle_ATC, update_plan_soin) {
    var url = new Url("hospi", "ajax_transmission");
    url.addParam("sejour_id", sejour_id);
    url.addParam("user_id", user_id);
    url.addParam("update_plan_soin", update_plan_soin);
    url.addNotNullParam("transmission_id", transmission_id);
    url.addNotNullParam("object_id",    object_id);
    url.addNotNullParam("object_class", object_class);
    url.addNotNullParam("libelle_ATC", libelle_ATC);
    url.requestModal(600, 400);
  };

  addCibleTransmission = function(sejour_id, object_class, object_id, libelle_ATC, update_plan_soin) {
    addTransmission(sejour_id, '{{$app->user_id}}', null, object_id, object_class, libelle_ATC, update_plan_soin);
  };

  viewBilanService = function(service_id, date){
    var url = new Url("hospi", "vw_bilan_service");
    url.addParam("service_id", service_id);
    url.addParam("date", date);
    url.popup(800,500,"Bilan par service");
  };

  printBons = function(service_id, date) {
    var url = new Url("prescription", "print_bon");
    url.addParam("service_id", service_id);
    url.addParam("debut", date);
    url.popup(800, 500);
  };

  Main.add(function() {
    fillCategories();
    updatePlanSoinsPatients();
    Calendar.regField(getForm("updateActivites").date);
  });
</script>

<form name="adm_multiple" action="?" method="get">
  <input type="hidden" name="_administrations">
</form>

<form name="addPlanif" action="" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_administration_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="administration_id" value="" />
  <input type="hidden" name="planification" value="1" />
  <input type="hidden" name="administrateur_id" value="" />
  <input type="hidden" name="dateTime" value="" />
  <input type="hidden" name="quantite" value="" />
  <input type="hidden" name="unite_prise" value="" />
  <input type="hidden" name="prise_id" value="" />
  <input type="hidden" name="object_id" value="" />
  <input type="hidden" name="object_class" value="" />
  <input type="hidden" name="original_dateTime" value="" />
</form>

<form name="addPlanifs" action="" method="post">
  <input type="hidden" name="m" value="prescription" />
  <input type="hidden" name="dosql" value="do_administrations_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="decalage" value="" />
  <input type="hidden" name="administrations_ids" value=""/>
  <input type="hidden" name="planification" value="1" />
</form>

<table class="main">
  <tr>
    <th class="title" colspan="3">
      <span style="float: right;">
        <button class="print" onclick="printBons('{{$service->_id}}', '{{$date}}')">Bons</button>
        <button class="search" onclick="viewBilanService('{{$service->_id}}', '{{$date}}');">Bilan</button>
      </span>
      <form name="updateActivites" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
        
        Gestion des activités du service
        <select name="service_id" onchange="this.form.submit();">
          <option value="">&mdash; Service</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service->_id}}selected{{/if}}>{{$_service->_view}}</option>
          {{/foreach}}
        </select>
        le
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit();" />
        <input type="checkbox" name="real_time_cb" onchange="$V('real_time', this.checked ? 1 : 0);" {{if $date != $day}}disabled{{elseif $real_time}}checked{{/if}}/>
        <input type="hidden" id="real_time" name="real_time" value="{{if $date == $day}}{{$real_time}}{{else}}0{{/if}}" onchange="this.form.submit();"/>
        <label for="real_time">{{tr}}Real time{{/tr}}</label>
      </form>
    </th>
  </tr>

  {{if $service->_id}}
    <tr>
      <td style="width: 20%;" id="categories">
        {{mb_include module=soins template=inc_form_elements}}
      </td>
      <td id="content_plan_soins_service">
      </td>
    </tr>
  {{else}}
    <tr>
      <td>
        <div class="small-info">
          Veuillez sélectionner un service pour accéder à la gestion des activités de celui-ci.
        </div>
      </td>
    </tr>
  {{/if}}
</table>