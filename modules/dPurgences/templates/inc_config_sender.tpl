{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-Sender" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_configure" />
  <table class="form">
    {{assign var="var" value="rpu_sender"}} 
    <tr>
     <th>
       <label for="{{$m}}[{{$var}}]" title="{{tr}}config-{{$m}}-{{$var}}{{/tr}}">
         {{tr}}config-{{$m}}-{{$var}}{{/tr}}
       </label>  
     </th>
     <td>
       <select name="{{$m}}[{{$var}}]">
         <option value="" {{if "" == $dPconfig.$m.$var}} selected="selected" {{/if}}>&mdash; Aucun</option>
         <option value="COscourSender" {{if "COscourSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}COscourSender{{/tr}}</option>
         <option value="COuralSender" {{if "COuralSender" == $dPconfig.$m.$var}} selected="selected" {{/if}}>{{tr}}COuralSender{{/tr}}</option>
       </select>
     </td>
    </tr>
    
    {{mb_include module=system template=inc_config_bool var=rpu_xml_validation}}
      
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
