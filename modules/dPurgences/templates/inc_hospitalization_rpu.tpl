{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=dPplanningOp script=sejour ajax=true}}

{{assign var=label value=$conf.dPurgences.create_sejour_hospit|ternary:"simple":"transfert"}}

<form name="confirmHospitalization" method="post" onsubmit="return onSubmitFormAjax(this, Control.Modal.close())">
  <input type="hidden" name="dosql" value="do_transfert_aed" />
  <input type="hidden" name="m" value="dPurgences" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
  {{if $sejour_collision}}<input type="hidden" name="sejour_id_merge" value="{{$sejour_collision->_id}}">{{/if}}
  <div class="small-info">{{tr}}confirm-RPU-Hospitalisation-{{$label}}{{/tr}}</div>
  {{if $count_collision > 1}}
    <div class="small-warning">
      Plusieurs collisions ont été détectées, aucun traitement possible.
      Veuillez contacter un administrateur pour fusionner les dossiers.
    </div>
  {{elseif $count_collision == 1}}
    <div class="small-warning">
      Le séjour
      <strong>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour_collision->_guid}}')">{{$sejour_collision->_view}}</span>
      </strong> est en collision avec ce séjour. La fusion est obligatoire.
    </div>
    {{if $check_merge}}
      <div class="small-error">
        Des Erreurs ont été détectées pour la fusion de séjour :
        {{$check_merge}}
      </div>
    {{/if}}
  {{/if}}
  {{if $sejours_futur}}
    <table class="tbl">
      <tr>
        <th class="title" colspan="5">Choix du séjour</th>
      </tr>
      <tr>
        <th>{{tr}}CSejour{{/tr}}</th>
        <th>{{mb_label class="CSejour" field="type"}}</th>
        <th>{{mb_label class="CSejour" field="_motif_complet"}}</th>
        <th>{{mb_label class="CSejour" field="praticien_id"}}</th>
        <th>Erreur fusion</th>
      </tr>
      <tr class="selected">
        <td class="narrow" colspan="5">
          <label>
            <input type="radio" name="sejour_id_merge" onclick="this.up('tr').addUniqueClassName('selected')" value="" checked>
            {{tr}}CSejour.create{{/tr}}
          </label>
        </td>
      </tr>
      {{foreach from=$sejours_futur item=_sejour_futur}}
        <tr>
          <td class="narrow">
            <label>
              <input type="radio" name="sejour_id_merge" value="{{$_sejour_futur->_id}}"
                     onclick="this.up('tr').addUniqueClassName('selected');
                                Urgences.checkMerge('{{$sejour->_id}}', '{{$_sejour_futur->_id}}')">
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour_futur->_guid}}')">
                    {{$_sejour_futur->_view}}
                  </span>
            </label>
          </td>
          <td class="narrow">{{tr}}CSejour.type.{{$_sejour_futur->type}}{{/tr}}</td>
          <td class="text compact">{{$_sejour_futur->_motif_complet}}</td>
          <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour_futur->_ref_praticien}}</td>
          <td id="result_merge_{{$_sejour_futur->_id}}"></td>
        </tr>
      {{/foreach}}
    </table>
    {{/if}}
    <div style="text-align: center;">
      <br/>
      <button class="close" type="button" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
      {{if $count_collision < 2}}
        <button class="tick" type="submit">
          {{if $count_collision == 1}}
            {{tr}}Merge{{/tr}}
          {{else}}
            {{tr}}Confirm{{/tr}}
          {{/if}}
        </button>
      {{/if}}
    </div>
</form>