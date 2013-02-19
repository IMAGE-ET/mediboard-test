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


<form>

  <fieldset>
    <legend>{{tr}}type_assurance.maternite{{/tr}}</legend>
    <table>
      <tr>
        <td>{{mb_label object=$consult field=grossesse_id}}</td>
        <td>{{mb_field object=$consult field=grossesse_id}}</td>
      </tr>
    </table>
  </fieldset>
</form>
