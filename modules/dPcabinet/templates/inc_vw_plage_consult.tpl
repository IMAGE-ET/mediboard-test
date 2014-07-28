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

{{*
 * $Id$
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>
  .barree {
    text-decoration: line-through;
  }
</style>

{{* plages op *}}
<div class="modal_month">
  <table class="tbl">
    <tr>
      <th colspan="4" class="title">
        {{mb_include module=system template=inc_object_notes}}
        {{mb_include module=system template=inc_object_idsante400}}
        {{mb_include module=system template=inc_object_history}}
        {{if $object->locked}}<img src="images/icons/lock.png" alt="(Vérouillée)"/>{{/if}}
        {{tr}}{{$object->_class}}{{/tr}} - {{mb_value object=$object field=date}}
      </th>
    </tr>
    <tr>
      <th class="narrow">{{mb_title object=$object field=chir_id}}</th>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_chir}}
      </td>
      <th class="narrow">{{mb_title object=$object field=freq}}</th>
      <td>{{$object->_freq}} minutes</td>
    </tr>
    <tr>
      <th>{{mb_title object=$object field=remplacant_id}}</th>
      <td>
        {{if $object->remplacant_id}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_remplacant}}
        {{else}}
          &mdash;
        {{/if}}
      </td>
      <th>horaires</th>
      <td>{{$object->debut}} &rarr; {{$object->fin}}</td>
    </tr>
  </table>
  <table class="tbl" style="max-height: 400px; overflow-y: auto;">
    <tr>
      <th colspan="6" class="title">{{tr}}CConsultation{{/tr}}s {{mb_include module=system template=calendars/inc_week/inc_disponibilities mode=standard}}</th>
    </tr>
    <tr>
      <th class="narrow">{{mb_title class=CConsultation field=heure}}</th>
      <th class="narrow" colspan="2">{{mb_title class=CConsultation field=patient_id}}</th>
      <th>{{mb_title class=CConsultation field=motif}}</th>
      <th class="narrow">{{mb_title class=CConsultation field=duree}}</th>
      <th class="narrow"></th>
    </tr>
    {{foreach from=$object->_ref_consultations item=_consult}}
      {{assign var=class value=""}}
      {{if !$_consult->patient_id}}
        {{assign var=class value="$class hatching"}}
      {{/if}}
      {{if $_consult->annule}}
        {{assign var=class value="$class barree"}}
      {{/if}}
      <tr>
        <td class="{{$class}}"><span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}');">{{$_consult->heure|date_format:$conf.time}}</span></td>
        {{if !$_consult->patient_id}}
          <td colspan="3" class="{{$class}}">[PAUSE] {{$_consult->motif}}</td>
        {{else}}
          <td style="text-align: center" class="{{$class}}">
            {{if $_consult->_ref_patient->_ref_photo_identite && $_consult->_ref_patient->_ref_photo_identite->_id}}
              <img src="?m=files&amp;a=fileviewer&amp;file_id={{$_consult->_ref_patient->_ref_photo_identite->_id}}&amp;phpThumb=1&amp;w=30" alt="" style="max-height: 30px; max-width: 30px;" />
            {{/if}}
          </td>
          <td class="{{$class}}"><span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_ref_patient->_guid}}');">
            {{$_consult->_ref_patient}}
          </span></td>
          <td class="{{$class}} compact text">{{$_consult->motif}}</td>
        {{/if}}
        <td class="{{$class}}">{{math equation="a*b" a=$_consult->duree b=$object->_freq}} min</td>
        <td class="{{$class}}">
          {{if $_consult->chrono == 64}}<img title="Terminée" src="images/icons/tick.png" alt="" style="max-height: 30px;"/> {{/if}}
        </td>
      </tr>
      {{foreachelse}}
      <tr>
        <td class="empty" colspan="6">{{tr}}CConsultation.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</div>
