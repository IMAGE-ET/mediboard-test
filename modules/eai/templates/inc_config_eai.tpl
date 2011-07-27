{{*
 * Configs EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<form name="editConfigEAI" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">
    {{assign var="mod" value="eai"}}
    <tr>
      <th class="title" colspan="10">{{tr}}config-{{$mod}}{{/tr}}</th>
    </tr>
    
    {{mb_include module=system template=inc_config_str var=exchange_format_delayed numeric=true}}

    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>