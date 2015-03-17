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
    <th class="category narrow"></th>
    <th class="category">{{tr}}CCIM10.DP{{/tr}}</th>
    <th class="category">{{tr}}CCIM10.DR{{/tr}}</th>
    <th class="category">{{tr}}CCIM10.DAS{{/tr}}</th>
  </tr>
  <tr>
    <td>{{tr}}CPatient{{/tr}}</td>
    <td class="empty">{{tr}}CCodeCIM10.none{{/tr}}</td>
    <td class="empty">{{tr}}CCodeCIM10.none{{/tr}}</td>
    <td class="text" style="vertical-align: top">
      <ul>
        {{foreach from=$patient->_ref_dossier_medical->_ext_codes_cim item=_code_cim}}
          <li>
            <!-- Si module atih on affiche l'aide au codage -->
            {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
              {{assign var=code value=$_code_cim->code}}
              {{if $cim_das_patient.$code}}
                <img src="images/icons/note_green.png">
              {{else}}
                <img src="images/icons/note_red.png">
              {{/if}}
            {{/if}}
            {{$_code_cim->code}} ({{$_code_cim->libelle}}
          </li>
        {{foreachelse}}
          <li class="empty">
            {{tr}}CCodeCIM10.none{{/tr}}
          </li>
        {{/foreach}}
      </ul>
    </td>
  </tr>


  <tr>
    <td>{{tr}}CSejour{{/tr}}</td>
    <td class="text" style="vertical-align: top">
      {{if $sejour->DP}}

        <!-- Si module atih on affiche l'aide au codage -->
        {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
          {{if $sejour->_DP_state}}
            <img src="images/icons/note_green.png">
          {{else}}
            <img src="images/icons/note_red.png">
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$sejour->_ext_diagnostic_principal->code}}')">
            {{$sejour->_ext_diagnostic_principal->code}} ({{$sejour->_ext_diagnostic_principal->libelle}})
          </span>
        {{else}}
          <span>{{$sejour->_ext_diagnostic_principal->code}} - {{$sejour->_ext_diagnostic_principal->libelle}}</span>
        {{/if}}
      {{else}}
        <span class="empty">{{tr}}CCodeCIM10.none{{/tr}}</span>
      {{/if}}
    </td>

    <td class="text" style="vertical-align: top">
      {{if $sejour->DR}}
        <!-- Si module atih on affiche l'aide au codage -->
        {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
          {{if $sejour->_DR_state}}
            <img src="images/icons/note_green.png">
          {{else}}
            <img src="images/icons/note_red.png">
          {{/if}}

          <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$sejour->_ext_diagnostic_relie->code}}')">
            {{$sejour->_ext_diagnostic_relie->code}} ({{$sejour->_ext_diagnostic_relie->libelle}})
          </span>
        {{else}}
          <span>{{$sejour->_ext_diagnostic_relie->code}} ({{$sejour->_ext_diagnostic_relie->libelle}})</span>
        {{/if}}

      {{else}}
        <span class="empty">{{tr}}CCodeCIM10.none{{/tr}}</span>
      {{/if}}
    </td>
    <td class="text" style="vertical-align: top">
      <ul>
        {{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item=_code_cim}}
          <li>

            <!-- Si module atih on affiche l'aide au codage -->
            {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
              {{assign var=code value=$_code_cim->code}}
              {{if $cim_das.$code}}
                <img src="images/icons/note_green.png">
              {{else}}
                <img src="images/icons/note_red.png">
              {{/if}}
              <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$_code_cim->code}}')">
                {{$_code_cim->code}} - ({{$_code_cim->libelle}})
              </span>
            {{else}}
              <span>{{$_code_cim->code}} - ({{$_code_cim->libelle}})</span>
            {{/if}}


          </li>
        {{foreachelse}}
          <li class="empty">
            <span>{{tr}}CCodeCIM10.none{{/tr}}</span>
          </li>
        {{/foreach}}
      </ul>
    </td>
  </tr>


  {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <tr>
      <td>{{tr}}CRSS{{/tr}}</td>
      <td class="text" style="vertical-align: top">
        {{if $rss->dp}}
          {{if $rss->_ref_DP->type == 0}}
            <img title="{{tr}}CCIM10.type.{{$rss->_ref_DP->type}}{{/tr}}" src="images/icons/note_green.png">
          {{else}}
            <img title="{{tr}}CCIM10.type.{{$rss->_ref_DP->type}}{{/tr}}" src="images/icons/note_red.png">
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$rss->_ref_DP->_id}}')">
            {{$rss->_ref_DP->code}} ({{$rss->_ref_DP->short_name|@strtolower}})
          </span>
        {{else}}
          <span class="empty">{{tr}}CCIM10.none{{/tr}}</span>
        {{/if}}
      </td>
      <td class="text" style="vertical-align: top">
        {{if $rss->dr}}
          {{if $rss->_ref_DR->type == 0 || $rss->_ref_DR->type == 4}}
            <img title="{{tr}}CCIM10.type.{{$rss->_ref_DR->type}}{{/tr}}" src="images/icons/note_green.png">
          {{else}}
            <img title="{{tr}}CCIM10.type.{{$rss->_ref_DR->type}}{{/tr}}" src="images/icons/note_red.png">
          {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$rss->_ref_DR->_id}}')">
            {{$rss->_ref_DR->code}} ({{$rss->_ref_DR->short_name|@strtolower}})
          </span>
        {{else}}
          <span class="empty">{{tr}}CCIM10.none{{/tr}}</span>
        {{/if}}
      </td>
      <td class="text" style="vertical-align: top">
        <ul>
          {{foreach from=$rss->_ref_DAS item=_da}}
            <li>
              {{if $_da->type != 3}}
                <img title="{{tr}}CCIM10.type.{{$_da->type}}{{/tr}}" src="images/icons/note_green.png">
              {{else}}
                <img title="{{tr}}CCIM10.type.{{$_da->type}}{{/tr}}" src="images/icons/note_red.png">
              {{/if}}
              <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$_da->_id}}')">
                {{$_da->code}} ({{$_da->short_name|@strtolower}})
              </span>
            </li>
            {{foreachelse}}
            <li class="empty">
              {{tr}}CCIM10.none{{/tr}}
            </li>
          {{/foreach}}
        </ul>
      </td>
    </tr>
  {{/if}}
</table>