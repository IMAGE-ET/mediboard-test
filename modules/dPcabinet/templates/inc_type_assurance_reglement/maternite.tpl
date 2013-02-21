{{*
  * Type d'assurance maternite
  *  
  * @category Cabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<fieldset>
  <legend>{{tr}}type_assurance.maternite{{/tr}}</legend>
  {{mb_form name="editCslt_maternite" method="post" onsubmit="return onSubmitFormAjax(this)" m="cabinet" dosql="do_consultation_aed"}}
    {{mb_key object=$consult}}
    <table>
      <tr>
        <td><label title="Date d'accouchement effective ou à défaut date présumée de début de grossesse" class="" for="editCslt_maternite_date_at" id="labelFor_editConsultation_date_at">Date accouchement prévue</label></td>
        <td>{{mb_field object=$consult field=date_at form=editCslt_maternite register=true onchange="this.form.onsubmit();"}}</td>
      </tr>
    </table>
  {{/mb_form}}
</fieldset>