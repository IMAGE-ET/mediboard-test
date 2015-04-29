{{*
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script>
  Main.add(function() {
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      DiagPMSI.getAutocompleteCim10PMSI(getForm("editDPPMSI"), getForm("editDPPMSI").dp);
    {{/if}}
    DiagPMSI.getAutocompleteCim10(getForm("editDP"), getForm("editDP").DP);
  });
</script>

<table class="tbl">
  <tr>
    <th class="category" colspan="2">{{tr}}PMSI.Diagnostic Principal{{/tr}}</th>
  </tr>
  {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <tr>
      <th class="section halfPane">{{tr}}CSejour{{/tr}}</th>
      <th class="section halfPane">{{tr}}CRSS{{/tr}}</th>
    </tr>
  {{/if}}
  <!--  Diagnostic Principal OMS (séjour)-->
  <tr>
    <td class="narrow">
      <form name="editDP" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
        {{mb_key object=$sejour}}
        {{mb_class object=$sejour}}
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <label for="keywords_code" title="{{tr}}PMSI-dp-desc{{/tr}}">{{tr}}PMSI-cim10-oms{{/tr}}</label>
        <input type="text" name="keywords_code" class="autocomplete str  code cim10" value="{{$sejour->DP}}" size="10"/>
        <input type="hidden" name="DP" onchange="this.form.onsubmit();"/>
        <button class="search notext" type="button" onclick="CIM10Selector.initDP({{$sejour->_id}})">
          {{tr}}Search{{/tr}}
        </button>
        </form>
    </td>

    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <!--  Diagnostic Principal ATIH (rss)-->
      <td>
        <form name="editDPPMSI" action="?m={{$m}}" method="post"
              onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
          {{mb_key object=$rss}}
          {{mb_class object=$rss}}
          <label for="keywords_code_pmsi" title="{{tr}}CRSS-dp-desc{{/tr}}">{{tr}}PMSI-cim10-atih{{/tr}}</label>
          <input type="text" name="keywords_code_pmsi" class="autocomplete str code cim10Pmsi" value="{{$rss->dp}}" size="10"/>
          <input type="hidden" name="dp" onchange="this.form.onsubmit();"/>
          <button class="search notext" type="button" onclick="DiagPMSI.initDiagCimPMSI(this.form.dp)">
            {{tr}}Search{{/tr}}
          </button>
        </form>
      </td>
    {{/if}}
  </tr>

  <tr>
    <!--  Diagnostic Principal avec CIM-10 OMS (séjour)-->
    <td>
      {{if $sejour->_ext_diagnostic_principal}}
        <ul class="tags" style="float: none;">
          <li class="tag" style="white-space:normal">
            <button type="button" class="delete notext" onclick="DiagPMSI.deleteDiag(getForm('editDP'), getForm('editDP').DP);" style="display: inline-block !important;"></button>
            {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
              <span class="circled {{if $sejour->_DP_state}}ok{{else}}error{{/if}}">
                  {{$sejour->_ext_diagnostic_principal->code}} - {{$sejour->_ext_diagnostic_principal->libelle}}
              </span>
            {{else}}
              {{$sejour->_ext_diagnostic_principal->code}} - {{$sejour->_ext_diagnostic_principal->libelle}}
            {{/if}}
          </li>
        </ul>
      {{/if}}
    </td>

    <!--  Diagnostic Principal avec CIM-10 à visée PMSI (rss)-->
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      <td class="text">
        {{if $rss->_ref_DP}}
          <ul class="tags" style="float: none;">
            <li class="tag" style="white-space: normal">
              <button type="button" class="delete notext" onclick="DiagPMSI.deleteDiag(getForm('editDPPMSI'), getForm('editDPPMSI').dp);" style="display: inline-block !important;"></button>
              <span title="{{tr}}CCIM10.type.{{$rss->_ref_DP->type}}{{/tr}}"
                  class="circled {{if $rss->_ref_DP->type == 0}}ok{{else}}error{{/if}}">
                {{$rss->_ref_DP->code}} - {{$rss->_ref_DP->complete_name}}
              </span>
            </li>
          </ul>
        {{/if}}
      </td>
    {{/if}}
  </tr>
</table>