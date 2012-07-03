{{* $Id: inc_vw_admission_line.tpl 13679 2011-11-04 17:46:47Z rhum1 $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 13679 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ddd"}}
{{/if}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td class="button" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $can->edit}}
  <form name="editAdmFrm{{$_sejour->_id}}" action="?m={{$m}}" method="post">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_sejour_aed" />
  <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
  <input type="hidden" name="patient_id" value="{{$patient->_id}}" />
  <input type="hidden" name="recuse" value="{{$_sejour->recuse}}" />
  
  {{assign var="_fiche" value=$_sejour->_ref_fiche_autonomie}}
  
  {{if $_sejour->recuse == "-1"}}
    <div style="white-space: nowrap;" onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')">En attente</div>
    <button type="button" class="tick notext" onclick="$V(this.form.recuse, '0'); this.form.submit();">
      {{tr}}OK{{/tr}}
    </button>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '1'); this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{elseif $_sejour->recuse == "1"}}
    <div style="white-space: nowrap;" onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')">Récusé</div>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '-1');  this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{else}}
    <div style="white-space: nowrap;" onmouseover="ObjectTooltip.createEx(this, '{{$_fiche->_guid}}')">Validé</div>
    <button type="button" class="cancel notext" onclick="$V(this.form.recuse, '-1');  this.form.submit();">
      {{tr}}Cancel{{/tr}}
    </button>
  {{/if}}
  </form>
  {{/if}}
</td>

<td colspan="2" class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $can->edit}}
  <a class="action" style="float: right" title="Modifier le séjour" href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}">
    <img src="images/icons/planning.png" />
  </a>
  {{/if}}
  {{if $canPlanningOp->read}}
  <a class="action" style="float: right" title="Imprimer la DHE du séjour" href="#1" onclick="printDHE('sejour_id', {{$_sejour->_id}}); return false;">
    <img src="images/icons/print.png" />
  </a>
  {{/if}}
  
  {{if $patient->_ref_IPP}}
  <script type="text/javascript">
    PatHprimSelector.init{{$patient->_id}} = function(){
      this.sForm      = "editIPP{{$patient->_id}}";
      this.sId        = "id400";
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  <form name="editIPP{{$patient->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$patient->_ref_IPP->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$patient->_ref_IPP->id400}}" />
    <input type="hidden" class="notNull" name="tag" value="{{$patient->_ref_IPP->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$patient->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CPatient" />
    <input type="hidden" name="last_update" value="{{$patient->_ref_IPP->last_update}}" />
  </form>
  
  <script type="text/javascript">
    SejourHprimSelector.init{{$_sejour->_id}} = function(){
      this.sForm      = "editNumdos{{$_sejour->_id}}";
      this.sId        = "id400";
      this.sIPPForm   = "editIPP{{$patient->_id}}";
      this.sIPPId     = "id400";
      this.sIPP       = document.forms.editIPP{{$patient->_id}}.id400.value;
      this.sPatNom    = "{{$patient->nom}}";
      this.sPatPrenom = "{{$patient->prenom}}";
      this.pop();
    };
  </script>
  {{if $_sejour->_ref_NDA}}
  <form name="editNumdos{{$_sejour->_id}}" action="?m={{$m}}" method="post" onsubmit="return ExtRefManager.submitNumdosForm({{$_sejour->_id}})">
    <input type="hidden" name="dosql" value="do_idsante400_aed" />
    <input type="hidden" name="m" value="dPsante400" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="ajax" value="1" />
    <input type="hidden" name="id_sante400_id" value="{{$_sejour->_ref_NDA->_id}}" />
    <input type="hidden" class="notNull" name="id400" value="{{$_sejour->_ref_NDA->id400}}" size="8" />
    <input type="hidden" class="notNull" name="tag" value="{{$_sejour->_ref_NDA->tag}}" />
    <input type="hidden" class="notNull" name="object_id" value="{{$_sejour->_id}}" />
    <input type="hidden" class="notNull" name="object_class" value="CSejour" />
    <input type="hidden" class="notNull" name="sejour_id" value="{{$_sejour->_id}}" />
    <input type="hidden" name="last_update" value="{{$_sejour->_ref_NDA->last_update}}" />
    {{if @$modules.hprim21}}
      <button type="button" class="edit notext" onclick="setExternalIds(this.form)">Edit external Ids</button>
    {{/if}}
  </form>
  {{/if}}
  {{/if}}
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{mb_value object=$_sejour field=libelle}}<br />
    {{$_sejour->entree_prevue|date_format:$conf.time}}
  </span>
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include module=admissions template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}" class="button">
  {{if $_sejour->_couvert_cmu}}
  <div><strong>CMU</strong></div>
  {{/if}}
  {{if $_sejour->_couvert_ald}}
  <div><strong {{if $_sejour->ald}}style="color: red;"{{/if}}>ALD</strong></div>
  {{/if}}
</td>