{{*  *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  redirectOffline = function(type) {
    switch(type) {
      case 'sejour':
        var url = new Url("soins", "offline_sejours");
        break;
      case 'bilan':
        var url = new Url("soins", "offline_bilan_service");
    }
    
    url.addParam("service_id", $("service_id").value);
    url.addParam("dialog", 1);
    url.redirect();
  }
</script>
<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
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

    <tr>
      <th class="category" colspan="2">
        Transmissions
      </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=cible_mandatory_trans}}
    {{mb_include module=system template=inc_config_bool var=trans_compact}}

    <tr>
       <th class="category" colspan="2">
         Autres paramètres
       </th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=show_charge_soins}}
    {{mb_include module=system template=inc_config_enum var=max_time_modif_suivi_soins values=$listHours skip_locales=1}}
    {{mb_include module=system template=inc_config_bool var=show_only_lit_bilan}}
    {{mb_include module=system template=inc_config_str var=ignore_allergies textarea=true}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>