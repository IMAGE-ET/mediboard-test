{{*
 * $Id$
 *  
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="infosInterv" action="?" method="post" onsubmit="onSubmitFormAjax(this, Control.Modal.close)">
  <input type="hidden" name="m" value="planningOp" />
  <input type="hidden" name="dosql" value="do_planning_aed" />
  {{mb_key object=$operation}}

  <table class="form">
    <tr>
      <th>{{mb_label object=$operation field="libelle"}}</th>
      <td>
        {{mb_field object=$operation field="libelle" form="infosInterv"
        autocomplete="true,1,50,true,true"
        style="width: 20em"}}
    </tr>
   <tr>
     <th>{{mb_label object=$operation field="cote"}}</th>
     <td>{{mb_field object=$operation field="cote"}}</td>
   </tr>
   <tr>
     <td colspan="2" class="button">
       <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
     </td>
   </tr>
  </table>
</form>