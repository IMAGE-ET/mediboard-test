{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if $type == "consultation"}}
  <table class="tbl">
    {{foreach from=$patient->_ref_consultations item=_consult}}
      {{if $_consult->_ref_chir->_ref_function->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
        <tr>
          <td {{if $_consult->annule == 1}}class="cancelled"{{/if}}>
            <a href="?m=dPcabinet&tab=edit_consultation&selConsult={{$_consult->_id}}"
               onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              le {{$_consult->_datetime|date_format:$conf.date}}
            </a>
          </td>
          <td {{if $_consult->annule == 1}}class="cancelled"{{/if}}>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
          </td>

          <td>
          </td>
        </tr>
      {{elseif $conf.dPpatients.CPatient.multi_group == "limited"}}
        <tr>
          <td {{if $_consult->annule == 1}}class="cancelled"{{/if}}>
            le {{$_consult->_datetime|date_format:$conf.date}}
          </td>
          <td style="background-color:#afa">
            {{$_consult->_ref_chir->_ref_function->_ref_group->text|upper}}
          </td>

          <td>
          </td>
        </tr>
      {{/if}}
      {{foreachelse}}

      <tr>
        <td colspan="2" class="empty">{{tr}}CConsultation.none{{/tr}}</td>
      </tr>
    {{/foreach}}

  </table>

  {{mb_return}}
{{/if}}

<table class="tbl">
  {{foreach from=$patient->_ref_sejours item=_sejour}}
    {{if $_sejour->group_id == $g || $conf.dPpatients.CPatient.multi_group == "full"}}
      <tr>
        <td {{if $_sejour->annule == 1}}class="cancelled"{{/if}}>
          <a href="?m=dPplanningOp&tab=vw_edit_sejour&sejour_id={{$_sejour->_id}}"
             onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
            {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
          </a>
        </td>
        <td {{if $_sejour->annule == 1}}class="cancelled"{{/if}}>
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        </td>
      </tr>
      {{foreach from=$_sejour->_ref_operations item=_op}}
        <tr>
          <td style="padding-left: 1em;" {{if $_op->annulee == 1}}class="cancelled"{{/if}}>
            <a href="?m=dPplanningOp&tab=vw_edit_planning&operation_id={{$_op->_id}}"
               onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
              le {{$_op->_datetime|date_format:$conf.date}}
            </a>
          </td>
          <td {{if $_op->annulee == 1}}class="cancelled"{{/if}}>
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="2" class="empty" style="padding-left: 1em;">
            {{tr}}COperation.none{{/tr}}
          </td>
        </tr>
      {{/foreach}}
    {{else}}
      <tr>
        <td {{if $_sejour->annule == 1}}class="cancelled"{{/if}}>
          {{mb_include module=system template=inc_interval_date from=$_sejour->entree to=$_sejour->sortie}}
        </td>
        <td style="background-color:#afa">
          {{$_sejour->_ref_group->text|upper}}
        </td>
      </tr>
    {{/if}}

    {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>