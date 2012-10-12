{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{if $date_move < $sejour->entree_prevue || $date_move > $sejour->sortie_prevue}}
  <div class="small-warning">
    L'intervention du {{$date_move|date_format:$conf.datetime}} n'est pas dans les bornes du séjour
  </div>
{{/if}}

<script type="text/javascript">
  checkDates = function(form) {
    if ($V(form.sortie_prevue) < '{{$date_move}}' || $V(form.entree_prevue) > '{{$date_move}}') {
      alert("La date d'intervention est toujours en dehors des dates prévues du séjour");
      return false;
    }
    return true;
  }
</script>

<form name="editSejour" method="post"
  onsubmit="if (checkDates(this)){
    {{if $callback}}
      return onSubmitFormAjax(this, {onComplete: {{$callback}} });
    {{else}}
      afterModifSejour(); return false;
    {{/if}}
    }">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="_check_bounds" value="0" />
  {{mb_key object=$sejour}}
  {{mb_field object=$sejour field=patient_id hidden=1}}
  
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        Date de l'intervention : {{$date_move|date_format:$conf.datetime}}
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label object=$sejour field=entree_prevue}}
      </th>
      <td>
        {{mb_field object=$sejour field=entree_prevue form=editSejour register=true}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$sejour field=sortie_prevue}}
      </th>
      <td>
        {{mb_field object=$sejour field=sortie_prevue form=editSejour register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>
