{{* $Id: inc_vw_admission_line.tpl 12345 2011-06-03 12:55:42Z flaviencrochard $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 12345 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <form name="editAffFrm{{$_aff->_id}}" action="?m=dPadmissions" method="post">
    <input type="hidden" name="m" value="dPhospi" />
    <input type="hidden" name="dosql" value="do_affectation_aed" />
    {{if $type_externe == "depart"}}
      {{if $_aff->_ref_prev->_id}}
        {{assign var=_affectation value=$_aff->_ref_prev}}
      {{else}}
        {{assign var=_affectation value=$_aff}}
      {{/if}}
      
      {{mb_key object=$_affectation}}
      {{if $_affectation->effectue}}
        <input type="hidden" name="effectue" value="0" />
        <button type="button" class="cancel" onclick="onSubmitFormAjax(this.form, { onComplete: function() {reloadPermission()} })">Annuler le départ</button>
      {{else}}
        <input type="hidden" name="effectue" value="1" />
        <button type="button" class="tick" onclick="onSubmitFormAjax(this.form, { onComplete: function() {reloadPermission()} })">Valider le départ</button>
      {{/if}}
    {{else}}
      {{mb_key object=$_aff}}
      {{if $_aff->effectue}}
        <input type="hidden" name="effectue" value="0" />
        <button type="submit" class="cancel">Annuler le retour</button>
      {{else}}
        <input type="hidden" name="effectue" value="1" />
        <button type="submit" class="tick">Valider le retour</button>
      {{/if}}
    {{/if}}
  </form>
</td>

<td colspan="2" class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <div style="float: right;">
    
  </div>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_aff->entree|date_format:$conf.time}}
  </span>
</td>

{{if $type_externe == "depart"}}

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{$_aff->_ref_prev->_ref_lit->_view}}
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{$_aff->_ref_lit->_view}}
</td>

{{else}}

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{$_aff->_ref_lit->_view}}
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{$_aff->_ref_next->_ref_lit->_view}}
</td>

{{/if}}

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{$_aff->_duree}} jour(s)
</td>