{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=colspan value=2}}
{{mb_default var=can_delete value=true}}
{{mb_default var=options value="{}"}}
{{mb_default var=options_ajax value="{}"}}


<tr>
  <td class="button" colspan="{{$colspan}}">
    {{if $object->_id}}
      <button class="save">{{tr}}Edit{{/tr}}</button>
      {{if $can_delete}}
        <button class="trash" type="button" onclick="
        confirmDeletion(this.form,
        {{$options}}
        ,
        {{$options_ajax}})
        ">{{tr}}Delete{{/tr}}</button>
      {{/if}}
    {{else}}
      <button class="save">{{tr}}Save{{/tr}}</button>
    {{/if}}
  </td>
</tr>