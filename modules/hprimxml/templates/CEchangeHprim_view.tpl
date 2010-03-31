{{* $Id: CMbObject_view.tpl 6069 2009-04-14 10:17:11Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
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