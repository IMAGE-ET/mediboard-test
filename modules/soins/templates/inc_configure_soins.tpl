{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="editConfig" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    <col style="width: 50%" />
    <tr>
      <th class="category" colspan="2">
        {{tr}}CLit{{/tr}}
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool class=CLit var=align_right}}

    <tr>
      <th class="category" colspan="2">
        {{tr}}CConstantesMedicales{{/tr}}
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=constantes_show}}

    <tr>
      <th class="category" colspan="2">
        {{tr}}Offline{{/tr}}
      </th>
    </tr>
    <tr>
      <td style="text-align: right">
        {{tr}}CChambre-service_id{{/tr}} :
        <select id="service_id">
          {{foreach from=$services item=_service}}
            <option value='{{$_service->_id}}'>{{$_service->nom}}</option>
          {{/foreach}}
        </select>
      </td>
      <td>
        <a class="button search" onclick="redirectOffline('sejour');">{{tr}}CSejour.all{{/tr}}</a>
        <a class="button download" onclick="redirectOffline('sejour', true);">{{tr}}Download{{/tr}} {{tr}}CSejour.all{{/tr}}</a>
        {{if "dPprescription"|module_active}}
          <br/>
          <a class="button search" onclick="redirectOffline('bilan');">{{tr}}CService.bilan{{/tr}}</a>
        {{/if}}
      </td>
    </tr>

    <tr>
      <th class="category" colspan="2">
        Pancarte des services
      </th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=transmissions_hours values="12|24|36|48" skip_locales=1}}
    {{mb_include module=system template=inc_config_enum var=soin_refresh_pancarte_service values="none|10|20|30"}}


    <tr>
      <th class="category" colspan="2">
        Transmissions
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=cible_mandatory_trans}}
    {{mb_include module=system template=inc_config_bool var=trans_compact}}

    <tr>
      <th class="category" colspan="2">
        Vue séjours
      </th>
    </tr>
    {{mb_include module=system template=inc_config_enum var=refresh_vw_sejours_frequency values="disabled|600|1200|1800"}}

    <tr>
      <th class="category" colspan="2">
        Autres paramètres
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=show_charge_soins}}
    {{mb_include module=system template=inc_config_enum var=max_time_modif_suivi_soins values=$listHours skip_locales=1}}
    {{mb_include module=system template=inc_config_bool var=show_only_lit_bilan}}
    {{mb_include module=system template=inc_config_str  var=ignore_allergies textarea=true}}
    {{mb_include module=system template=inc_config_bool var=vue_condensee_dossier_soins}}



    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>