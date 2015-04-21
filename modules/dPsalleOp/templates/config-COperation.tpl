{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-COperation" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  
  <table class="form">
    {{mb_include module=system template=inc_config_bool var=mode_anesth}}  
    {{mb_include module=system template=inc_config_bool var=enable_surveillance_perop}}

    {{assign var="class" value="COperation"}}
    
    <tr>
      <th class="title" colspan="2">{{tr}}config-{{$m}}-{{$class}}{{/tr}}</th>
    </tr>
  
    {{mb_include module=system template=inc_config_bool var=mode}}
    {{mb_include module=system template=inc_config_bool var=modif_salle}}
    {{mb_include module=system template=inc_config_bool var=use_check_timing}}

    {{assign var="var" value="modif_actes"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$class}}][{{$var}}]" title="{{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$class}}-{{$var}}{{/tr}}
        </label>    
      </th>
      <td>
        <select class="str" name="{{$m}}[{{$class}}][{{$var}}]">
          <option value="never" {{if $conf.$m.$class.$var == "never"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-never{{/tr}}</option>
          <option value="oneday" {{if $conf.$m.$class.$var == "oneday"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-oneday{{/tr}}</option>
          <option value="button" {{if $conf.$m.$class.$var == "button"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-button{{/tr}}</option>
          <option value="facturation" {{if $conf.$m.$class.$var == "facturation"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-facturation{{/tr}}</option>
          <option value="48h" {{if $conf.$m.$class.$var == "48h"}}selected="selected"{{/if}}>{{tr}}config-{{$m}}-{{$class}}-{{$var}}-48h{{/tr}}</option>
        </select>
      </td>             
    </tr> 

    <tr>
      <th class="title" colspan="6">Listes déroulantes des timings</th>
    </tr>
    
    {{assign var="var" value="max_sub_minutes"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>    
      </th>
      <td>
        <input type="text" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}"/> 
      </td>             
    </tr>
    
    {{assign var="var" value="max_add_minutes"}}
    <tr>
      <th>
        <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
          {{tr}}config-{{$m}}-{{$var}}{{/tr}}
        </label>    
      </th>
      <td>
        <input type="text" name="{{$m}}[{{$var}}]" value="{{$conf.$m.$var}}"/> 
      </td>             
    </tr>
  
    <tr>
      <th class="title" colspan="2">Affichage des timings</th>
    </tr>
    {{mb_include module=system template=inc_config_bool var=use_entree_sortie_salle}}
    {{mb_include module=system template=inc_config_bool var=use_garrot}}
    {{mb_include module=system template=inc_config_bool var=use_debut_fin_op}}
    {{mb_include module=system template=inc_config_bool var=use_entree_bloc}}
    {{mb_include module=system template=inc_config_bool var=use_remise_chir}}

    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>