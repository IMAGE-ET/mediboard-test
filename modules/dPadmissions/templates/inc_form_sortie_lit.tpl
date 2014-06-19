{{*
 * $Id$
 *  
 * @category dPadmissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tr>
  <th>Lit</th>
  <td colspan="3">
    <select name="lit_id" style="width: 15em;" onchange="Admissions.choisirLit(this);">
      <option value="">&mdash; Choisir Lit </option>
      {{foreach from=$lits item=_lit}}
        {{assign var=chambre value=$_lit->_ref_chambre}}
        {{assign var=service value=$chambre->_ref_service}}
        <option id="{{$_lit->_guid}}" value="{{$_lit->lit_id}}" data-service_id="{{$service->_id}}" data-name="{{$service->nom}}"
                {{if $_lit->_view|strpos:"bloqué"}}disabled{{/if}}
                {{if $_lit->lit_id == $sejour->_ref_curr_affectation->lit_id}}selected{{/if}}>
          {{$_lit->_view}}
        </option>
      {{/foreach}}
    </select>
  </td>
</tr>