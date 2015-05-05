{{* $Id: inc_vw_antecedent_allergie.tpl 10644 2010-11-16 15:46:22Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 10644 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=dossier_medical_sejour value=0}}

<!-- Affichage des autres antecedents -->
 {{if $dossier_medical->_count_antecedents || ($dossier_medical_sejour && $dossier_medical_sejour->_count_antecedents)}}
   <span class="texticon texticon-atcd" onmouseover="ObjectTooltip.createDOM(this, 'antecedents{{$sejour_id}}')">Atcd</span>

  <div id="antecedents{{$sejour_id}}" style="text-align:left;  display: none;">
    <table class="tbl">
      <tr>
        <th {{if $dossier_medical->_count_antecedents && $dossier_medical_sejour && $dossier_medical_sejour->_count_antecedents}}colspan="2"{{/if}} class="title">
          {{tr}}CAntecedent.more{{/tr}} {{if $dossier_medical_sejour && $dossier_medical_sejour->_count_antecedents && !$dossier_medical->_count_antecedents}}significatifs{{/if}}
        </th>
      </tr>
      {{if $dossier_medical_sejour && $dossier_medical_sejour->_count_antecedents && $dossier_medical->_count_antecedents}}
        <th class="category">
          Significatifs
        </th>
        <th class="category">
          Autres antécédents
        </th>
      {{/if}}
      <tr>
        {{if $dossier_medical_sejour && $dossier_medical_sejour->_count_antecedents}}
          <td class="halfPane" style="padding: 0; vertical-align: top;">
            {{mb_include module=soins template=inc_list_antecedents antecedents=$antecedents_sejour dossier_medical=$dossier_medical_sejour}}
          </td>
        {{/if}}
        {{if $dossier_medical->_count_antecedents}}
          <td class="halfPane" style="padding: 0; vertical-align: top;">
            {{mb_include module=soins template=inc_list_antecedents antecedents=$antecedents dossier_medical=$dossier_medical}}
          </td>
        {{/if}}
      </tr>
    </table>
  </div>
 {{/if}}