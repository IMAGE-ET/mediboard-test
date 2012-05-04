{{* $Id: inc_vw_antecedent_allergie.tpl 10644 2010-11-16 15:46:22Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10644 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Affichage des autres antecedents -->
 {{if $dossier_medical->_count_antecedents && ($dossier_medical->_count_antecedents > $dossier_medical->_count_allergies)}}
  <img src="images/icons/antecedents.gif" onmouseover="ObjectTooltip.createDOM(this, 'antecedents{{$sejour_id}}')" />
  
   <div id="antecedents{{$sejour_id}}" style="text-align:left;  display: none;">
     <table class="tbl">
      <tr>
        <th class="title">
          Antécédents
        </th>
      </tr>
      {{foreach from=$antecedents key=name item=cat}}
        {{if $name != "alle" && $cat|@count}}
          <tr>
            <th>
              {{tr}}CAntecedent.type.{{$name}}{{/tr}}
            </th>
          </tr>
          {{foreach from=$cat item=ant}}
            <tr>
              <td>
                {{if $ant->date}}
                  {{mb_value object=$ant field=date}}:
                {{/if}}
                {{$ant->rques}}
              </td>
            </tr>
          {{/foreach}}
        {{/if}}
      {{/foreach}}
      </table>
   </div>  
 {{/if}}