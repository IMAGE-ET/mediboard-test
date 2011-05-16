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
        <br/>
        <a class="button search" onclick="redirectOffline('bilan');">{{tr}}CService.bilan{{/tr}}</a>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>