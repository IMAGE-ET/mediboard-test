{{*
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module="maternite" script="allaitement" ajax=1}}
{{assign var=grossesse   value=$patient->_ref_last_grossesse}}
{{assign var=allaitement value=$patient->_ref_last_allaitement}}

<fieldset id="etat_actuel_grossesse">
  <legend>Etat actuel</legend>

  <table class="layout">
    <tr>
      <td class="text">
        <strong>Grossesse : </strong>
        {{if $grossesse->_id}}
          {{$grossesse}}
        {{else}}
          &mdash;
        {{/if}}
      </td>
      <td class="narrow">
        <button type="button" class="add notext" style="float: right;" onclick="Grossesse.viewGrossesses('{{$patient->_id}}', null, null, 0)"></button>
      </td>
    </tr>
    <tr>
      <td class="text">
        <strong>Allaitement :</strong>
        {{if $allaitement->_id}}
          {{$allaitement}}
        {{else}}
          &mdash;
        {{/if}}
      </td>
      <td class="narrow">
        <button type="button" class="add notext" style="float: right;" onclick="Allaitement.viewAllaitements('{{$patient->_id}}')"></button>
      </td>
    </tr>
  </table>
</fieldset>