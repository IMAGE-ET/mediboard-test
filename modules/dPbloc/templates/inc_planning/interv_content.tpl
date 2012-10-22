{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Intervention -->
<td class="text">
  {{if $curr_op->exam_extempo}}
    <img src="images/icons/extempo.png" title="{{tr}}COperation-exam_extempo{{/tr}}" style="float: right;"/>
  {{/if}}
  {{if $curr_plageop|is_array || $curr_plageop->spec_id}}
    <strong>Dr {{$curr_op->_ref_chir}}</strong>
    <br />
  {{/if}}
  {{if $curr_op->libelle}}
    {{$curr_op->libelle}}
    <br />
  {{/if}}
  {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
    {{if !$curr_code->_code7}}<strong>{{/if}}
    <em>{{$curr_code->code}}</em>
    {{if $filter->_ccam_libelle}}
      : {{$curr_code->libelleLong|truncate:60:"...":false}}
      <br/>
    {{else}}
      ;
    {{/if}}
    {{if !$curr_code->_code7}}</strong>{{/if}}
  {{/foreach}}
  {{assign var=consult_anesth value=$curr_op->_ref_consult_anesth}}
  {{if $curr_op->rques || ($consult_anesth && $consult_anesth->_intub_difficile)}}
    <div class="small-warning">
      <em>{{mb_label object=$curr_op field=rques}}</em> :
      {{mb_value object=$curr_op field=rques}}
      {{if $consult_anesth->_id && $consult_anesth->_intub_difficile}}
        <div style="font-weight: bold; color:#f00;">
          {{tr}}CConsultAnesth-_intub_difficile{{/tr}}
        </div>
      {{/if}}
    </div>
  {{/if}}
</td>
<td class="button">{{$curr_op->cote|truncate:1:""|capitalize}}</td>
<td class="{{if $curr_op->type_anesth != null}}text{{else}}button{{/if}}">
  {{if $curr_op->type_anesth != null}}
  {{$curr_op->_lu_type_anesth}}
  {{else}}
  &mdash;
  {{/if}}
</td>
<td class="text">
  {{if $curr_op->exam_extempo}}
    <strong>{{mb_title object=$curr_op field=exam_extempo}}</strong>
    <br />
  {{/if}}
  {{$curr_op->rques|nl2br}}
</td>
<td class="text">
  {{if $curr_op->commande_mat == '0' && $curr_op->materiel != ''}}
  <em>Materiel manquant:</em>
  {{/if}}
  {{$curr_op->materiel|nl2br}}
</td>