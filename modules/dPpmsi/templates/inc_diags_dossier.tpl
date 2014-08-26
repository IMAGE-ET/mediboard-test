{{*
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="halfPane category">{{tr}}CPatient{{/tr}}</th>
    <th class="category">{{tr}}CSejour{{/tr}}</th>
  </tr>
  <tr>
    <td class="text" style="vertical-align: top">
      <ul>
        {{foreach from=$patient->_ref_dossier_medical->_ext_codes_cim item=_code_cim}}
          <li>
            {{$_code_cim->code}} ({{$_code_cim->libelle}}
          </li>
        {{foreachelse}}
          <li class="empty">
            {{tr}}CCodeCIM10.none{{/tr}}
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td class="text" style="vertical-align: top">
      {{mb_label object=$sejour field=DP}} :
      {{if $sejour->DP}}
        {{$sejour->_ext_diagnostic_principal->code}} ({{$sejour->_ext_diagnostic_principal->libelle}})
      {{else}}
        <span class="empty">{{tr}}CCodeCIM10.none{{/tr}}</span>
      {{/if}}

      <hr />

      {{mb_label object=$sejour field=DR}} :

      {{if $sejour->DR}}
        {{$sejour->_ext_diagnostic_relie->code}} ({{$sejour->_ext_diagnostic_relie->libelle}})
      {{else}}
        <span class="empty">{{tr}}CCodeCIM10.none{{/tr}}</span>
      {{/if}}

      <hr />

      <label title="Diagnostics associés significatifs">DAS</label> :

      <ul>
        {{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item=_code_cim}}
          <li>
            {{$_code_cim->code}} ({{$_code_cim->libelle}})
          </li>
        {{foreachelse}}
          <li class="empty">
            {{tr}}CCodeCIM10.none{{/tr}}
          </li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
</table>