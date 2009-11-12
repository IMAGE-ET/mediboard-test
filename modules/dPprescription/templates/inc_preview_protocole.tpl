{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="preview_protocole" method="get" action="?">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="a" value="{{$a}}" />
  <input type="hidden" name="dialog" value="1" />
	<input type="hidden" name="pack_id" value="{{$pack_id}}" />
	<input type="hidden" name="protocole_id" value="{{$protocole_id}}" />
	
<table class="tbl">
  <tr>
    <th colspan="4" class="title">Apercu du plan de soin</th>
  </tr>
  <tr>
    <td>Début séjour</td>
    <td>
      {{mb_field object=$sejour field=_entree register=true form=preview_protocole}}
    </td>
    <td>Fin séjour</td>
    <td>
      {{mb_field object=$sejour field=_sortie register=true form=preview_protocole}}
    </td>
  </tr>
  <tr>
    <td>Date de l'operation</td>
    <td>
      {{mb_field object=$operation field=_datetime register=true form=preview_protocole}}
    </td>
    <td colspan="2">
      <button class="tick" onclick="this.form.submit();">Afficher le plan de soin</button>
    </td>
  </tr>
</table>
</form>

{{if $sejour->_entree && $sejour->_sortie && $operation->_datetime}}
  {{include file="../../dPprescription/templates/vw_plan_soin.tpl"}}
{{else}}
  <div class="small-info">
    Veuillez sélectionner une date de debut et une date de fin du séjour pour visualiser l'apercu du plan de soin.
  </div>
{{/if}}