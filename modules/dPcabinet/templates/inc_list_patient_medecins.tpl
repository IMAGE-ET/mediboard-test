{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}
 
{{assign var=medecin value=$patient->_ref_medecin_traitant}}

<form name="editAdresseParPrat" method="post" action="?" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  {{mb_key object=$consult}}
  
  <label>
    {{mb_field object=$consult field=adresse typeEnum=checkbox 
               onchange="togglePatientAddresse(this)"}}
    {{tr}}CConsultation-adresse{{/tr}}
  </label>
  <br />
  
  {{assign var=medecin_found value=false}}
  
  {{if $medecin->_id}}
    <label onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
      <input type="radio" name="adresse_par_prat_id" value="{{$medecin->_id}}" class="adresse_par"
             {{if !$consult->adresse}}style="visibility:hidden"{{/if}}
             {{if $consult->adresse_par_prat_id == $medecin->_id}}
               {{assign var=medecin_found value=true}}
               checked="checked"
             {{/if}}
             onclick="this.form.onsubmit()" /> 
      <strong>{{$medecin}}</strong>
    </label>
    <br />
  {{/if}}
  
  {{foreach from=$patient->_ref_medecins_correspondants item=curr_corresp}}
    {{assign var=medecin value=$curr_corresp->_ref_medecin}}
    <label onmouseover="ObjectTooltip.createEx(this, '{{$medecin->_guid}}');">
      <input type="radio" name="adresse_par_prat_id" value="{{$medecin->_id}}" class="adresse_par"
             {{if !$consult->adresse}}style="visibility:hidden"{{/if}}
             {{if $consult->adresse_par_prat_id == $medecin->_id}}
               {{assign var=medecin_found value=true}}
               checked="checked"
             {{/if}}
             onclick="this.form.onsubmit()" /> 
      {{$medecin}}
    </label>
    <br />
  {{/foreach}}
  
  <div class="adresse_par" {{if !$consult->adresse}}style="visibility:hidden"{{/if}}>
    <input type="radio" name="adresse_par_prat_id" value="{{if !$medecin_found}}{{$consult->adresse_par_prat_id}}{{/if}}" class="adresse_par"
           {{if !$medecin_found && $consult->adresse_par_prat_id}}checked="checked"{{/if}}
           onclick="Medecin.edit()" />
    <button type="button" class="search" onclick="$(this).previous('input').checked=true;Medecin.edit()">{{tr}}Other{{/tr}}</button> 
    <span>
      {{if !$medecin_found && $consult->adresse_par_prat_id}}
        {{$consult->_ref_adresse_par_prat}}
      {{/if}}
    </span>
    <button type="button" class="add notext" onclick="addOtherCorrespondant($V(this.previous('input')))"
      {{if $medecin_found || !$consult->adresse_par_prat_id}}style="display: none"{{/if}}>
      </button>
  </div>
</form>