{{*
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 *}}
<script>
  Main.add(function () {
    Control.Tabs.create('tabs-liste-actes', true);
    {{foreach from=$sejour->_ref_operations item=_op}}
      PMSI.loadExportActes('{{$_op->_id}}', 'COperation');
    {{/foreach}}
    PMSI.loadExportActes('{{$sejour->_id}}', 'CSejour', null, "{{$m}}" );
    PMSI.loadDiagsDossier('{{$sejour->_id}}');
    PMSI.loadDiagsPMSI('{{$sejour->_id}}');
  });
</script>

<table class="main layout">
  <tr>
    <td style="white-space:nowrap;" class="narrow">
      <ul id="tabs-liste-actes" class="control_tabs_vertical">
        {{* Séjour *}}
        <li>
          <a href="#{{$sejour->_guid}}" class="{{if $sejour->_count_actes == 0}}empty{{/if}} {{if $sejour->annule}}cancelled{{/if}}"
            >Sejour (<span id="count_actes_{{$sejour->_guid}}">{{$sejour->_count_actes}}</span>)
            <br/>
            <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}</span>
          </a>
        </li>
        {{* Interventions *}}
        {{foreach from=$sejour->_ref_operations item=_op}}
          <li>
            <a href="#{{$_op->_guid}}" class="{{if $_op->_count_actes == 0}}empty{{/if}} {{if $_op->annulee}}cancelled{{/if}}"
              >Intervention du {{$_op->_datetime|date_format:$conf.date}} (<span id="count_actes_{{$_op->_guid}}">{{$_op->_count_actes}}</span>)
              <br/>
              <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_praticien}}</span>
            </a>
          </li>
        {{/foreach}}
        {{* Consultations liées au séjour *}}
        {{foreach from=$sejour->_ref_consultations item=_consult}}
          <li>
            <a href="#{{$_consult->_guid}}" class="{{if $_consult->_count_actes == 0}}empty{{/if}} {{if $_consult->annule}}cancelled{{/if}}"
              >Consultation du {{$_consult->_ref_plageconsult->date|date_format:$conf.date}}
                (<span id="count_actes_{{$_consult->_guid}}">{{$_consult->_count_actes}}</span>)
              <br/>
              <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_praticien}}</span>
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td>
      {{* Séjour *}}
      <div id="{{$sejour->_guid}}" style="display: none;">
        {{mb_include module=pmsi template=inc_vw_actes_pmsi_sejour}}
      </div>
      {{* Interventions *}}
      {{foreach from=$sejour->_ref_operations item=_op}}
        <div id="{{$_op->_guid}}" style="display: none;">
          {{mb_include module=pmsi template=inc_vw_actes_pmsi_interv operation=$_op}}
        </div>
      {{/foreach}}
      {{* Consultations liées au séjour *}}
      {{foreach from=$sejour->_ref_consultations item=_consult}}
        <div id="{{$_consult->_guid}}" style="display: none;">
          {{mb_include module=pmsi template=inc_header_actes subject=$_consult}}
          {{mb_include module=pmsi template=inc_codage_actes subject=$_consult}}
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>