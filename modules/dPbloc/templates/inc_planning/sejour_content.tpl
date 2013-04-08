{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Sejour -->
<td class="button">
  {{$sejour->type|truncate:1:""|capitalize}}
  {{if $sejour->type == "comp"}}
    - {{$sejour->_duree_prevue}}j
  {{/if}}
</td>
<td class="text">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
    {{mb_value object=$sejour field=entree}}
  </span>
  {{if $_print_numdoss && $sejour->_NDA}}
    [{{$sejour->_NDA}}]
  {{/if}}
</td>
{{assign var="affectation" value=$sejour->_ref_first_affectation}}
<td class="{{if $affectation->_id}}text{{else}}button{{/if}}">
  {{if $affectation->_id}}
    {{$affectation->_ref_lit->_view}}
  {{else}}
    &mdash;
  {{/if}}
</td>