{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}



<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="selFacture" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <table class="form">
      	<tr>
      		<td>
      		<button type="button" class="new" onclick="showElementFacture('0',this)">{{tr}}CFactureItem-title-create{{/tr}}</button>
      		</td>
      	</tr>
        <tr>
          <th class="title" colspan="100">{{tr}}CFacture-select{{/tr}}</th>
        </tr>
        <tr>
          <th>
            <label for="facture_id" title="{{tr}}CFacture-select-desc{{/tr}}">{{tr}}CFacture{{/tr}}: </label>
          </th>
          <td>
            <select name="facture_id" onchange="submit()">
              <option value="">&mdash; {{tr}}CFacture-choix{{/tr}} &mdash;</option>
              {{foreach from=$listFacture item=curr_facture}}
                <option value="{{$curr_facture->facture_id}}" {{if $curr_facture->facture_id == $facture->facture_id}} selected="selected" {{/if}}  >
                  {{$curr_facture->facture_id}} / {{$curr_facture->_view}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
       </table> 
      </form>
      {{include file="inc_list_element.tpl"}}  
    </td>
    
    <!-- 
    <td style="width: 40%" id="vw_element">
      {{mb_include template=inc_edit_element}}
    </td>
     -->

  </tr>
</table>