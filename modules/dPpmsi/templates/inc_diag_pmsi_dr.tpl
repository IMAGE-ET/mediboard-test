{{*
 * $Id$
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script>
  Main.add(function() {
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      DiagPMSI.getAutocompleteCim10PMSI(getForm("editDRPMSI"), getForm("editDRPMSI").dr);
    {{/if}}
    DiagPMSI.getAutocompleteCim10(getForm("editDR"), getForm("editDR").DR);
  });
</script>

<!--  Diagnostic Relié avec CIM10 à visée PMSI-->
<table class="tbl">
  <tr>
    <th class="category" colspan="2">{{tr}}PMSI.Diagnostic Relie{{/tr}}</th>
  </tr>
  {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <tr>
      <th class="section halfPane">{{tr}}CSejour{{/tr}}</th>
      <th class="section halfPane">{{tr}}CRSS{{/tr}}</th>
    </tr>
  {{/if}}
  <!--  Diagnostic Relié avec CIM10 OMS (séjour)-->
  <tr>
    <td class="narrow">
      <form name="editDR" action="?m={{$m}}" method="post"
          onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'))">
        <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        {{mb_key object=$sejour}}
        {{mb_class object=$sejour}}

        <label for="keywords_code" title="{{tr}}PMSI-dr-desc{{/tr}}">{{tr}}PMSI-cim10-oms{{/tr}}</label>
        <input type="text" name="keywords_code" class="autocomplete str code cim10" value="{{$sejour->DR}}" size="10"/>
        <input type="hidden" name="DR" onchange="this.form.onsubmit();"/>
        <button class="search notext" type="button" onclick="CIM10Selector.initDR({{$sejour->_id}})">
          {{tr}}Search{{/tr}}
        </button>
      </form>
    </td>

    <!--  Diagnostic Relié avec CIM10 ATIH (rss)-->
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <td>
      <form name="editDRPMSI" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
        {{mb_key object=$rss}}
        {{mb_class object=$rss}}
        <label for="keywords_code_pmsi" title="{{tr}}CRSS-dr-desc{{/tr}}">{{tr}}PMSI-cim10-atih{{/tr}}</label>
        <input type="text" name="keywords_code_pmsi" class="autocomplete str code cim10Pmsi" value="{{$rss->dr}}" size="10"/>
        <input type="hidden" name="dr" onchange="this.form.onsubmit();"/>
        <button class="search notext" type="button" onclick="CIM10Selector.initDR({{$sejour->_id}})">
          {{tr}}Search{{/tr}}
        </button>
      </form>
    </td>
    {{/if}}
  </tr>

  <tr>
    <!--  Diagnostic Relié avec CIM10 OMS (séjour)-->
    <td>
      {{if $sejour->_ext_diagnostic_relie}}
        <ul class="tags" style="float: none;">
          <li class="tag">
            <button type="button" class="delete notext" onclick="DiagPMSI.deleteDiag(getForm('editDR'), 'DR');" style="display: inline-block !important;"></button>
            {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
              <span class="circled {{if $sejour->_DR_state}}ok{{else}}error{{/if}}">
                {{$sejour->_ext_diagnostic_relie->code}} - {{$sejour->_ext_diagnostic_relie->libelle}}
              </span>
            {{else}}
              {{$sejour->_ext_diagnostic_relie->code}} - {{$sejour->_ext_diagnostic_relie->libelle}}
            {{/if}}
          </li>
        </ul>
      {{/if}}
    </td>

    <!--  Diagnostic Relié avec CIM10 atih (rss)-->
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      <td>
        {{if $rss->_ref_DR}}
          <ul class="tags" style="float: none;">
            <li class="tag">
              <button type="button" class="delete notext" onclick="DiagPMSI.deleteDiag(getForm('editDRPMSI'), getForm('editDRPMSI').dr);" style="display: inline-block !important;"></button>
              <span title="{{tr}}CCIM10.type.{{$rss->_ref_DR->type}}{{/tr}}"
                    class="circled {{if $rss->_ref_DR->type == 0 || $rss->_ref_DR->type == 4}}ok{{else}}error{{/if}}">
                {{$rss->_ref_DR->code}}- {{$rss->_ref_DR->complete_name}}
              </span>
            </li>
          </ul>
        {{/if}}
      </td>
    {{/if}}
  </tr>

</table>