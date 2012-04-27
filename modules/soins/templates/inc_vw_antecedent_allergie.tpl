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
     <ul>
      {{foreach from=$antecedents key=name item=cat}}
      {{if $name != "alle" && $cat|@count}}
      <li>
      <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
      <ul>
      {{foreach from=$cat item=ant}}
      <li>
        {{if $ant->date}}
          {{mb_value object=$ant field=date}}:
        {{/if}}
        {{$ant->rques}}
      </li>
      {{/foreach}}
      </ul>
      </li>
      {{/if}}
      {{/foreach}}
   </ul>   
   </div>  
 {{/if}}