{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-CPlageOp" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{assign var="var" value="planning"}}
    {{foreach from=$conf.$m.$class.$var item=value key=col}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}][{{$col}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$col}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}-{{$col}}{{/tr}}
        </label>
      </th>
      <td>
        <select name="{{$m}}[{{$class}}][{{$var}}][{{$col}}]">
          <option value="patient" {{if $value=="patient"}} selected="selected"{{/if}}>Patient</option>
          <option value="sejour" {{if $value=="sejour"}} selected="selected"{{/if}}>Sejour</option>
          <option value="interv" {{if $value=="interv"}} selected="selected"{{/if}}>Intervention</option>
        </select>
      </td>   
    </tr>
    {{/foreach}}
    
    {{mb_include module=system template=inc_config_bool var=plage_vide}}
    {{mb_include module=system template=inc_config_bool var=libelle_ccam}}
    {{mb_include module=system template=inc_config_bool var=view_materiel}}
    {{mb_include module=system template=inc_config_bool var=view_extra}}
    {{mb_include module=system template=inc_config_bool var=view_duree}}

    <tr>
      <td class="button" colspan="100">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>