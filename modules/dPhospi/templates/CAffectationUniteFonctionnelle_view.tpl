{{* $Id: CAffectationUniteFonctionnelle_view.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var="affectations" value=$object}}

<table class="tbl tooltip">
  <tr>
    <th class="title text">
      {{$object->_view}}
    </th>
  </tr>
 {{foreach from=$affectations item=affectation}} 
  <tr>
    <td>
      <strong>{{mb_label object=$affectation field=uf_id}}</strong> :
      <em>{{$affectation->_ref_uf->_view}}</em>
    </td>
  </tr>
	{{/foreach}}
</table>