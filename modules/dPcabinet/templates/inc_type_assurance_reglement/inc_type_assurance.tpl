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
changeType = function(type) {
  var url = new Url("cabinet", "ajax_type_assurance");
  url.addParam("type", type);
  url.addParam("consult_id", '{{$consult->_id}}');
  url.requestUpdate("area_type_assurance");
}

</script>

<fieldset>
  <legend>{{tr}}type_assurance{{/tr}}</legend>
  <label><input type="radio" name="type" value="classique" onclick="changeType('assurance_classique')"/>Classique</label>
  <label><input type="radio" name="type" value="at" onclick="changeType('accident_travail')"/>Accident du travail</label>
  {{if $consult->_ref_patient->is_smg}}<label><input type="radio" name="type" value="soins_medicaux_gratuit" onclick="changeType('soins_medicaux_gratuits')"/>SMG</label>{{/if}}
  {{if "maternite"|module_active}}<label><input type="radio" name="type" value="maternite" onclick="changeType('maternite')"/>Maternité</label>{{/if}}
</fieldset>
