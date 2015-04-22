{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<!-- Intervention -->
<td class="text">
  {{if $curr_op->exam_extempo}}
    <img src="images/icons/extempo.png" title="{{tr}}COperation-exam_extempo{{/tr}}" style="float: right;"/>
  {{/if}}
  {{if $curr_plageop|is_array || ($curr_plageop && $curr_plageop->spec_id)}}
    <strong>Dr {{$curr_op->_ref_chir}}</strong>
    <br />
  {{/if}}
  {{if $curr_op->libelle}}
    {{$curr_op->libelle}}
    <br />
  {{/if}}
  {{foreach from=$curr_op->_ext_codes_ccam item=_code}}
    {{if !$curr_op->libelle}}
      {{if !$_code->_code7}}<strong>{{/if}}
      <em>{{$_code->code}}</em>
      {{if $filter->_ccam_libelle}}
        : {{$_code->libelleLong|truncate:60:"...":false}}
        <br/>
      {{else}}
        ;
      {{/if}}
      {{if !$_code->_code7}}</strong>{{/if}}
    {{/if}}
  {{/foreach}}
</td>
<td class="button">{{$curr_op->cote|truncate:1:""|capitalize}}</td>
<td class="{{if $curr_op->type_anesth != null}}text{{else}}button{{/if}}">
  {{if $curr_op->type_anesth != null}}
    {{$curr_op->_lu_type_anesth}}
  {{/if}}
  {{if $curr_op->anesth_id}}
    <br /> {{$curr_op->_ref_anesth->_view}}
  {{/if}}
  {{if !$curr_op->_ref_consult_anesth->_id && ($conf.dPbloc.CPlageOp.show_anesth_alerts && !$_compact)}}
    <div class="small-warning">{{tr}}COperation-back-dossiers_anesthesie.empty{{/tr}} informatisé</div>
  {{/if}}
</td>

{{if !$_compact}}
  <td class="text">
    {{if $curr_op->exam_extempo}}
      <strong>{{mb_title object=$curr_op field=exam_extempo}}</strong>
      <br />
    {{/if}}
    {{assign var=consult_anesth value=$curr_op->_ref_consult_anesth}}
    {{mb_include module=bloc template=inc_rques_intub operation=$curr_op}}
  </td>

  {{if $_materiel}}
    <td class="text">
      {{if $conf.dPbloc.CPlageOp.systeme_materiel == 'expert'}}
        {{foreach from=$curr_op->_ref_besoins item=_besoin name=ressources}}
          {{if !$smarty.foreach.ressources.first}}
            <br/>
          {{/if}}
          <span style="display: inline-block; width: 10px; height: 10px; background-color: #{{$_besoin->_color}};"></span>
          {{mb_value object=$_besoin->_ref_type_ressource field=libelle}}
        {{/foreach}}
      {{else}}
        {{if $curr_op->commande_mat == '0' && $curr_op->materiel != '' && $_missing_materiel}}
          <em>Materiel manquant:</em>
        {{/if}}
        {{$curr_op->materiel|nl2br}}
      {{/if}}
    </td>
  {{/if}}
{{else}}
  <td class="text">
    {{if $curr_op->exam_extempo}}
      <strong>{{mb_title object=$curr_op field=exam_extempo}}</strong>
      <br />
    {{/if}}
    {{assign var=consult_anesth value=$curr_op->_ref_consult_anesth}}
    {{mb_include module=bloc template=inc_rques_intub operation=$curr_op}}

    {{if $_materiel}}
      {{if $conf.dPbloc.CPlageOp.systeme_materiel == 'expert'}}
        {{foreach from=$curr_op->_ref_besoins item=_besoin name=ressources}}
          {{if !$smarty.foreach.ressources.first}}
            <br/>
          {{/if}}
          <span style="display: inline-block; width: 10px; height: 10px; background-color: #{{$_besoin->_color}};"></span>
          {{mb_value object=$_besoin->_ref_type_ressource field=libelle}}
        {{/foreach}}
      {{else}}
        {{if $curr_op->commande_mat == '0' && $curr_op->materiel != ''}}
          <em>Materiel manquant:</em>
        {{/if}}
        {{$curr_op->materiel|nl2br}}
      {{/if}}
    {{/if}}
  </td>
{{/if}}

{{if $_extra}}
  <td class="text" style="width: 10%">
    {{if $curr_op->plageop_id && $curr_op->_ref_plageop->salle_id != $curr_op->salle_id}}
      Déplacée en {{$curr_op->_ref_salle}} <br />
    {{/if}}

    {{foreach from=$curr_op->_ref_affectations_personnel key=type_personnel item=_affectations}}
      {{if ($type_personnel == "op" || $type_personnel == "op_panseuse" || $type_personnel == "iade" || $type_personnel == "sagefemme" || $type_personnel == "manipulateur") && $_affectations|@count > 0}}
        <strong>{{tr}}CPersonnel.emplacement.{{$type_personnel}}{{/tr}}</strong>
        <ul>
          {{foreach from=$_affectations item=_affectation}}
            <li>
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_affectation->_ref_personnel->_ref_user}}
            </li>
          {{/foreach}}
        </ul>
      {{/if}}
    {{/foreach}}
  </td>
{{/if}}
{{if $_duree}}
  <td>{{mb_value object=$curr_op field=_duree_interv}}</td>
{{/if}}
{{if $_by_prat}}
  <td class="text">
    {{$curr_op->_ref_salle}}

    {{if !$curr_op->plageop_id}}
      <br /> Hors plage
    {{/if}}
  </td>
{{/if}}