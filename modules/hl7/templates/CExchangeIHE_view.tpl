{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{include file=CMbObject_view.tpl}}

<table class="tbl tooltip">
  <tr>
    <td class="text">
      {{foreach from=$object->_observations item=_observation}}
        <strong>Code :</strong> {{$_observation.code}} <br />
        <strong>Libelle :</strong> {{$_observation.libelle}} <br />
        <strong>Commentaire :</strong> {{$_observation.commentaire}} <br />
      {{/foreach}}
    </td>
  </tr>
</table>

