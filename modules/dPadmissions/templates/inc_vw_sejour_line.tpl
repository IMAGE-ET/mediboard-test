{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPadmissions
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td class="button">
  {{if $can->edit}}
  <form name="editAdmFrm{{$_sejour->_id}}" action="?m={{$current_m}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="postRedirect" value="m={{$m}}" />
  <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
  <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
  <input type="hidden" name="recuse" value="{{$_sejour->recuse}}" />
  
  {{assign var="_fiche" value=$_sejour->_ref_fiche_autonomie}}
  
  {{if $_sejour->recuse == "-1"}}
    <div style="white-space: nowrap;" {{if $_fiche}}onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')"{{/if}}>En attente</div>
    <button type="button" class="tick notext" onclick="$V(this.form.recuse, '0'); this.form.submit();">
      {{tr}}OK{{/tr}}
    </button>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '1'); this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{elseif $_sejour->recuse == "1"}}
    <div style="white-space: nowrap;" {{if $_fiche}}onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')"{{/if}}>Récusé</div>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '-1');  this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{else}}
    <div style="white-space: nowrap;" {{if $_fiche}}onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')"{{/if}}>Validé</div>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '-1');  this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{/if}}
  </form>
  {{/if}}
</td>

<td colspan="2" class="text">
  {{if $_sejour->_envoi_mail}}
   <img src="style/mediboard/images/buttons/mail.png" style="float: right;" title="Mail répondu"/>
  {{/if}}
  {{if $can->edit}}
    {{if $current_m == "ssr"}}
      {{assign var=url value="?m=ssr&tab=vw_aed_sejour_ssr&sejour_id=`$_sejour->_id`"}}
    {{elseif $current_m == "reservation"}}
      {{assign var=url value="?m=reservation&tab=vw_edit_sejour&sejour_id=`$_sejour->_id`"}}
    {{else}}
      {{assign var=url value="?m=planningOp&tab=vw_edit_sejour&sejour_id=`$_sejour->_id`"}}
    {{/if}}
    <a class="action" style="float: right" title="Modifier le séjour" href="{{$url}}">
      <img src="images/icons/planning.png" />
    </a>
  {{/if}}
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right" title="Imprimer la DHE du séjour" href="#1" onclick="printDHE('sejour_id', {{$_sejour->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{/if}}

  {{if $patient->_ref_IPP}}
    <form name="editIPP{{$patient->_id}}" method="post">
      <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
      <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    </form>
  {{/if}}
  {{if $_sejour->_ref_NDA}}
    <form name="editNumdos{{$_sejour->_id}}" method="post">
      <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}"/>
      <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
      <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    </form>
  {{/if}}
  {{if "dPsante400"|module_active}}
    {{mb_include module=dPsante400 template=inc_manually_ipp_nda sejour=$_sejour patient=$patient callback=reloadAdmission}}
  {{/if}}
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{mb_value object=$_sejour field=libelle}}<br />
    {{$_sejour->entree_prevue|date_format:$conf.time}}
  </span>
</td>

<td>
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include module=admissions template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>

<td class="button">
  {{if $_sejour->_couvert_cmu}}
  <div><strong>CMU</strong></div>
  {{/if}}
  {{if $_sejour->_couvert_ald}}
  <div><strong {{if $_sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
  {{/if}}
</td>