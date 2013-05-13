{{* $Id: inc_form_message.tpl 13336 2011-09-29 14:49:19Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 13336 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="Create-Message-Update" action="?m=system" method="get" onsubmit="return Message.onSubmitUpdate(this);">
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{tr}}CMessage-title-create_update{{/tr}}
      </th>
    </tr>

    <tr>
      <th class="narrow">{{mb_label object=$message field="_update_moment"}}</th>
      <td>{{mb_field object=$message field="_update_moment" canNull=false form="Create-Message-Update" register=true}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="_update_initiator"}}</th>
      <td>{{mb_field object=$message field="_update_initiator" canNull=false}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="_update_benefits"}}</th>
      <td>{{mb_field object=$message field="_update_benefits" rows="5"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        <button class="right" type="submit">{{tr}}Continue{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>