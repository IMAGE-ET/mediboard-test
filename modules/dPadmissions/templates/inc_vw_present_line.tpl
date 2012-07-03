{{* $Id: inc_vw_admission_line.tpl 15057 2012-03-29 08:07:50Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 15057 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if     $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
{{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
{{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
{{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
{{else}}
{{assign var=background value="#ccc"}}
{{/if}}

{{assign var="patient" value=$_sejour->_ref_patient}}

<td colspan="2" class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if $canPlanningOp->read}}
    <div style="float: right;">
      {{if "web100T"|module_active}}
        {{mb_include module=web100T template=inc_button_iframe}}
      {{/if}}
      
      {{if $conf.dPadmissions.show_deficience}}
        {{mb_include module=patients template=inc_vw_antecedents type=deficience}}
      {{/if}}
      
      {{foreach from=$_sejour->_ref_operations item=_op}}
      <a class="action" title="Imprimer la DHE de l'intervention" href="#printDHE" onclick="Admissions.printDHE('operation_id', {{$_op->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{foreachelse}}
      <a class="action" title="Imprimer la DHE du séjour" href="#printDHE" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
        <img src="images/icons/print.png" />
      </a>
      {{/foreach}}
        
      <a class="action" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
        <img src="images/icons/planning.png" />
      </a>
      
      {{mb_include module=system template=inc_object_notes object=$_sejour}}
    </div>
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
  <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
  <span class="CPatient-view" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
    {{$patient}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span {{if $_sejour->entree|date_format:"%Y-%m-%d" == $date}}style="color: #070;"{{/if}}
    onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
    {{$_sejour->entree|date_format:$conf.datetime}}
  </span>
  <div style="position: relative;">
  <div class="sejour-bar" title="arrivée il y a {{$_sejour->_entree_relative}}j et départ prévu dans {{$_sejour->_sortie_relative}}j ">
    <div style="width: {{if $_sejour->_duree}}{{math equation='100*(-entree / (duree))' entree=$_sejour->_entree_relative duree=$_sejour->_duree format='%.2f'}}{{else}}100{{/if}}%;"></div>
  </div>
  </div>
</td>

<td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  <span {{if $_sejour->sortie|date_format:"%Y-%m-%d" == $date}}style="color: #070;"{{/if}}>
    {{$_sejour->sortie|date_format:$conf.datetime}}
  </span>
</td>

<td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
  {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
    {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
    {{mb_include module=hospi template=inc_placement_sejour sejour=$_sejour}}
  {{/if}}  
</td>