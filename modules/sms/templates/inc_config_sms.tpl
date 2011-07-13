{{* $Id: configure.tpl 8207 2010-03-04 17:05:05Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage sms
 * @version $Revision: 8207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editConfigSMS" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="sms"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=configure_handler class_handler=CSmsObjectHandler}}
    
    <tr>
      <th class="category" colspan="10">{{tr}}config-traitement-{{$mod}}{{/tr}}</th>
    </tr>
        
    {{mb_include module=system template=inc_config_bool var=server}}
    
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>