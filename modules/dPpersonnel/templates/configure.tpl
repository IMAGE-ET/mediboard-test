{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  affUpdate = function() {
    var url = new Url("personnel", "ajax_update_affectations");
    url.addParam("step", $V(getForm("Configure").step));
    url.requestUpdate("aff_area");
  }
</script>

<form name="Configure" action="?m={{$m}}&{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form"> 
	
	  {{assign var=class value=CPlageConge}}
	  <tr>
	    <th class="category" colspan="10">{{tr}}{{$class}}{{/tr}}</th>
	  </tr>
  
    {{mb_include module=system template=inc_config_bool var=show_replacer}}
    
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>

    <tr>
      <th class="category" colspan="2">
        Utilitaires
      </th>
    </tr>
    <tr>
      <td>
        <input type="text" name="step" value="0" />
        <button type="button" class="change" onclick="affUpdate()">Mettre à jour les affectations (plages opératoires)</button>
      </td>
      <td id="aff_area"></td>
    </tr>
  </table>
</form>