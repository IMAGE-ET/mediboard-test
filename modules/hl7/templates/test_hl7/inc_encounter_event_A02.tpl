{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  refreshMouvements = function() {
    //compatibilité avec dpHospi

    Control.Modal.close();
  };

  changeLit = function(affectation_id, link_affectation, datetime) {
    var url = new Url('hospi', 'ajax_suggest_lit');
    url.addParam('affectation_id', affectation_id);
    url.addParam("datetime", datetime);
    if (link_affectation) {
      url.addParam("_link_affectation", link_affectation);
    }

    url.requestModal(700, 400);
  }
</script>

{{mb_include module=hl7 template=inc_banner_event_hl7}}

{{assign var="formName" value="test_hl7_event$event"}}

<form method="post" name="{{$formName}}" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="hl7">
  <input type="hidden" name="dosql" value="do_encounter_event">
  <input type="hidden" name="event" value="{{$event}}">
  <input type="hidden" name="patient_id" value="{{$patient->_id}}">
  <input type="hidden" name="callback" value="Control.Modal.close">
  <table class="form">
    {{foreach from=$patient->_ref_sejours item=_sejour}}
      <tr>
        <td>
          <label>
            <input type="radio" name="sejour_id" value="{{$_sejour->_id}}">
            {{$_sejour->_view}} [{{if $_sejour->_NDA}}{{$_sejour->_NDA}}{{else}}-{{/if}}]
          </label>
        </td>
        <td>
          <button type="button" class="edit" onclick="Affectation.from_tempo=true; Affectation.edit('{{$_sejour->_ref_curr_affectation->_id}}')">
            {{tr}}CAffectation{{/tr}}
          </button>
        </td>
      </tr>
    {{foreachelse}}
      <tr><td><span class="empty">{{tr}}CSejour.none{{/tr}}</span></td></tr>
    {{/foreach}}
  </table>
</form>