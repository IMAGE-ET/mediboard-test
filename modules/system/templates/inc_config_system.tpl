{{* $Id: configure.tpl 7814 2010-01-12 17:26:57Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7814 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfig-system" action="?m=system&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{mb_include module=system template=inc_config_enum var=instance_role values="prod|qualif"}}
    
    {{mb_include module=system template=inc_config_str var=page_title}}
    
    {{mb_include module=system template=inc_config_str var=mb_id}}
    
    {{mb_include module=system template=inc_config_str m=system var=reverse_proxy}}
    
    {{mb_include module=system template=inc_config_str m=system var=website_url}}
        
    <tr>
      <td class="button" colspan="2">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>