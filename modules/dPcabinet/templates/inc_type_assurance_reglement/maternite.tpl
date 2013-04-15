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
  {{mb_form name="editCslt_maternite" method="post" onsubmit="return onSubmitFormAjax(this)" m="maternite"}}
    {{mb_class object=$consult->_ref_grossesse}}
    {{mb_key object=$consult->_ref_grossesse}}
    <table>
      <tr>
        <td><label title="Date d'accouchement effective ou à défaut date présumée de début de grossesse">Date accouchement prévue</label></td>
        <td>{{mb_field object=$consult->_ref_grossesse field=terme_prevu form=editCslt_maternite register=true onchange="this.form.onsubmit();"}}</td>
      </tr>
    </table>
  {{/mb_form}}
</fieldset>