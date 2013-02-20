{{*
  * Select the type of assurance
  *  
  * @category Cabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script>
changeType = function() {
  var url = new Url("cabinet", "ajax_type_assurance");
  url.addParam("consult_id", '{{$consult->_id}}');
  url.requestUpdate("area_type_assurance");
}

</script>

<fieldset>
  <legend>{{tr}}type_assurance{{/tr}}</legend>
  {{mb_form name="editCslt-type_assurance" method="post" onsubmit="return onSubmitFormAjax(this)" m="cabinet" dosql="do_consultation_aed"}}
    {{mb_key object=$consult}}
    <input type="hidden" name="callback" value="changeType()"/>

    <label><input type="radio" name="type_assurance" id="type_classique" value="classique" onclick="this.form.onsubmit();" {{if $consult->type_assurance == "classique"}} checked=true{{/if}}/>Classique</label>
    <label><input type="radio" name="type_assurance" id="type_at" value="at" onclick="this.form.onsubmit();" {{if $consult->type_assurance == "at"}} checked=true{{/if}}/>Accident du travail</label>
    {{if $consult->_ref_patient->is_smg}}
      <label><input type="radio" name="type_assurance" id="type_smg" value="smg" onclick="this.form.onsubmit();" {{if $consult->type_assurance == "smg"}} checked=true{{/if}}/>SMG</label>
    {{/if}}
    {{if "maternite"|module_active}}
      <label><input type="radio" name="type_assurance" id="type_maternite" value="maternite" onclick="this.form.onsubmit();" {{if $consult->type_assurance == "grossesse"}} checked=true{{/if}}/>Maternité</label>
    {{/if}}
  {{/mb_form}}
</fieldset>
