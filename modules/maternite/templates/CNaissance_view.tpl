{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
{{mb_default var=show_edit value=true}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{mb_include module=system template=CMbObject_view}}

{{assign var=naissance value=$object}}

{{mb_script module=maternite script=naissance ajax=1}}
{{if $show_edit}}
  <table class="form">
    <tr>
      <td class="button">
        <button class="edit" onclick="Naissance.edit('{{$naissance->_id}}')">
          {{tr}}Edit{{/tr}}
        </button>
      </td>
    </tr>
  </table>
{{/if}}
