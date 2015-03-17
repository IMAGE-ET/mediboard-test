{{*
 * $Id$
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<script type="text/javascript">
  Main.add(function(){
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      DiagPMSI.getAutocompleteCim10PMSI(getForm("editDAPMSI"), getForm("editDAPMSI")._added_das, true);
    {{/if}}
    DiagPMSI.getAutocompleteCim10(getForm("editDA"), getForm("editDA")._added_code_cim, true);
  });
</script>
<table class="tbl">
  <tr>
    <th class="category" colspan="2">{{tr}}PMSI.Diagnostic Associe{{/tr}}</th>
  </tr>
  {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
    <tr>
      <th class="section halfPane">{{tr}}CSejour{{/tr}}</th>
      <th class="section halfPane">{{tr}}CRSS{{/tr}}</th>
    </tr>
  {{/if}}
  <tr>
    <!--  Diagnostics Associés du dossier médical (OMS)-->
    <td>
      <form name="editDA" action="?m={{$m}}" method="post"
            onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_class" value="CSejour" />
        <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
        <input type="hidden" name="_praticien_id" value="{{$sejour->praticien_id}}" />
        <input type="hidden" name="_added_code_cim" onchange="this.form.onsubmit();"/>

        <label for="_added_code_cim" title="{{tr}}PMSI.Diagnostic Associe{{/tr}}">{{tr}}PMSI-cim10-oms{{/tr}}</label>
        <input type="text" name="keywords_code" class="autocomplete str" value="" size="10"/>
        <button class="search notext" type="button" onclick="CIM10Selector.initDAS({{$sejour->_id}})">
          {{tr}}Search{{/tr}}
        </button>
      </form>
    </td>

    <!--  Diagnostics Associés du rss (atih)-->
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      <td>
        <form name="editDAPMSI" action="?m={{$m}}" method="post"
              onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
          {{mb_key object=$rss}}
          {{mb_class object=$rss}}
          <input type="hidden" name="_added_das" onchange="this.form.onsubmit();"/>

          <label for="_added_das" title="{{tr}}CRSS-das-desc{{/tr}}">{{tr}}PMSI-cim10-atih{{/tr}}</label>
          <input type="text" name="keywords_code_pmsi" class="autocomplete str code cim10Pmsi" value="" size="10"/>
           <button class="search notext" type="button" onclick="CIM10Selector.initDAS({{$sejour->_id}})">{{tr}}Search{{/tr}}</button>
        </form>
      </td>
    {{/if}}
  </tr>
  <tr>

    <!--  Liste des Diagnostics Associés du dossier médical (OMS)-->
    <td style="vertical-align: top">
      <ul class="tags" style="float: none;">
        {{foreach from=$sejour->_ref_dossier_medical->_ext_codes_cim item="curr_cim"}}
          <li class="tag">
            <form name="delCodeAsso-{{$curr_cim->code}}" action="?m={{$m}}" method="post"
                  onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
              <input type="hidden" name="m" value="patients" />
              <input type="hidden" name="dosql" value="do_dossierMedical_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="object_class" value="CSejour" />
              <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
              <input type="hidden" name="_deleted_code_cim" value="{{$curr_cim->code}}" />
              <button class="delete notext" type="submit" style="display: inline-block !important;">{{tr}}Delete{{/tr}}</button>
            </form>
            {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
              {{assign var=code value=$curr_cim->code}}
              <span class="circled {{if $cim_das.$code}}ok{{else}}error{{/if}}">
                {{$curr_cim->code}} - {{$curr_cim->libelle}}
              </span>
            {{else}}
              {{$curr_cim->code}} - {{$curr_cim->libelle}}
            {{/if}}
          </li>
          <br/>
        {{/foreach}}
      </ul>
    </td>

    <!--  Liste des Diagnostics Associés du rss (ATIH)-->
    {{if "atih"|module_active && $conf.dPpmsi.use_cim_pmsi == "1"}}
      <td style="vertical-align: top">
        <ul class="tags" style="float: none;">
          {{foreach from=$rss->_ref_DAS item=_da}}
            <li class="tag">
              <form name="delCodeAsso-{{$_da->code}}" action="?m={{$m}}" method="post"
                    onsubmit="return onSubmitFormAjax(this, PMSI.afterEditDiag.curry('{{$sejour->_id}}'));">
                {{mb_key object=$rss}}
                {{mb_class object=$rss}}
                <input type="hidden" name="_deleted_das" value="{{$_da->code}}" />

                <button class="delete notext" type="submit" style="display: inline-block !important;">{{tr}}Delete{{/tr}}</button>
              </form>
              <span title="{{tr}}CCIM10.type.{{$_da->type}}{{/tr}}"
                    class="text circled {{if $_da->type != 3}}ok{{else}}error{{/if}}">
                {{$_da->code}} - {{$_da->complete_name}}
              </span>
            </li>
            <br/>
          {{/foreach}}
        </ul>
      </td>
    {{/if}}
  </tr>
</table>