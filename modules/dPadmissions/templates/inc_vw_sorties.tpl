{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=planningOp script=prestations ajax=1}}

<script type="text/javascript">
  Main.add(function() {
    Admissions.restoreSelection('listSorties');
    Calendar.regField(getForm("changeDateSorties").date, null, {noView: true});
    Prestations.callback = reloadSorties;
  });
</script>

<table class="tbl" id="sortie">
  <tr>
    <th class="title" colspan="10">
      <a href="?m=dPadmissions&tab=vw_idx_sortie&date={{$hier}}" style="display: inline"><<<</a>
      {{$date|date_format:$conf.longdate}}
      <form name="changeDateSorties" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_idx_sortie" />
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
      <a href="?m=dPadmissions&tab=vw_idx_sortie&date={{$demain}}" style="display: inline">>>></a>
      
      <br />
      
      <em style="float: left; font-weight: normal;">
      {{$sejours|@count}}
      {{if $selSortis == "n"}}sorties non effectuées
      {{else}}sorties ce jour
      {{/if}}
      </em>
  
      <select style="float: right" name="filterFunction" style="width: 16em;" onchange="reloadSorties(this.value);">
        <option value=""> &mdash; Toutes les fonctions</option>
        {{foreach from=$functions item=_function}}
          <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
        {{/foreach}}
      </select>
      
      {{if $type == "ambu"}}
      <button class="print" type="button" onclick="printAmbu()">Impression ambu</button>
      {{/if}}
    </th>
  </tr>
  
  {{assign var=url value="?m=$m&tab=vw_idx_sortie&selSortis=$selSortis"}}
  <tr>
    <th class="narrow">Effectuer la sortie</th>
    <th>
      <input type="checkbox" style="float: left;" onclick="Admissions.togglePrint('sortie', this.checked)"/>
      {{mb_colonne class="CSejour" field="patient_id" order_col=$order_col order_way=$order_way url="$url"}}
    </th>
    <th class="narrow">
      <input type="text" size="3" onkeyup="Admissions.filter(this, 'sortie')" id="filter-patient-name" />
    </th>
    <th>
      {{mb_colonne class="CSejour" field="praticien_id" order_col=$order_col order_way=$order_way url="?$url"}}
    </th>
    <th>
      {{mb_colonne class="CSejour" field="sortie_prevue" order_col=$order_col order_way=$order_way url="$url"}}
    </th>
    <th>Chambre</th>
    {{if $conf.dPadmissions.show_dh}}
      <th>DH</th>
    {{/if}}
  </tr>
  
  {{foreach from=$sejours item=_sejour}}
  {{if $_sejour->type == 'ambu'}} {{assign var=background value="#faa"}}
  {{elseif $_sejour->type == 'comp'}} {{assign var=background value="#fff"}}
  {{elseif $_sejour->type == 'exte'}} {{assign var=background value="#afa"}}
  {{elseif $_sejour->type == 'consult'}} {{assign var=background value="#cfdfff"}}
  {{else}}
  {{assign var=background value="#ccc"}}
  {{/if}}
  <tr>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if $canAdmissions->edit}}
      
      <script type="text/javascript">
        Main.add(function(){
          // Ceci doit rester ici !! prepareForm necessaire car pas appelé au premier refresh d'un periodical update
          prepareForm("editFrm{{$_sejour->_guid}}");
        });
      </script>
      
      <form name="editFrm{{$_sejour->_guid}}" action="?m={{$m}}" method="post" data-patient_view="{{$_sejour->_ref_patient->_view}}">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="sejour_id" value="{{$_sejour->_id}}" />
        <input type="hidden" name="type" value="{{$_sejour->type}}" />
        {{if $_sejour->grossesse_id}}
          <input type="hidden" name="_sejours_enfants_ids" value="{{","|implode:$_sejour->_sejours_enfants_ids}}" />
        {{/if}}
        {{if $_sejour->sortie_reelle}}
          <input type="hidden" name="mode_sortie" value="{{$_sejour->mode_sortie}}" />
          <input type="hidden" name="etablissement_sortie_id" value="{{$_sejour->etablissement_sortie_id}}" />
          <input type="hidden" name="_modifier_sortie" value="0" />
          <button class="cancel" type="button" onclick="submitSortie(this.form)">
            Annuler la sortie
          </button>
          
          <br />
          {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
            {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
          {{else}}
            {{$_sejour->sortie_reelle|date_format:$conf.time}}
          {{/if}}
          
          - {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}
          
          {{if $_sejour->etablissement_sortie_id}}
            - {{$_sejour->_ref_etablissement_transfert}}
          {{/if}}
        {{else}}
          <input type="hidden" name="_modifier_sortie" value="1" />
          <input type="hidden" name="entree_reelle" value="{{$_sejour->entree_reelle}}" />
          
          <div style="white-space: nowrap;">
            {{mb_field object=$_sejour field="mode_sortie" onchange="this.form._modifier_sortie.value = '0'; submitSortie(this.form);"}}
            <button class="tick" type="button" onclick="confirmation('{{$date_actuelle}}', '{{$date_demain}}', '{{$_sejour->sortie_prevue}}', '{{$_sejour->entree_reelle}}', this.form);">
              Effectuer la sortie
            </button>
          </div>
          <div id="listEtabExterne-editFrm{{$_sejour->_guid}}" {{if $_sejour->mode_sortie != "transfert"}} style="display: none;" {{/if}}>
            {{mb_field object=$_sejour field="etablissement_sortie_id" form="editFrm`$_sejour->_guid`" 
              autocomplete="true,1,50,true,true" onchange="changeEtablissementId(this.form)"}}
          </div>
        {{/if}}
      </form>
      
      {{elseif $_sejour->sortie_reelle}}
        {{if ($_sejour->sortie_reelle < $date_min) || ($_sejour->sortie_reelle > $date_max)}}
          {{$_sejour->sortie_reelle|date_format:$conf.datetime}}
        {{else}}
          {{$_sejour->sortie_reelle|date_format:$conf.time}}
        {{/if}}
        
        {{if $_sejour->mode_sortie}}
          <br />
          {{tr}}CSejour.mode_sortie.{{$_sejour->mode_sortie}}{{/tr}}
        {{/if}}
        
        {{if $_sejour->etablissement_sortie_id}}
          <br />{{$_sejour->_ref_etablissement_transfert}}
        {{/if}}
      {{else}}
        -
      {{/if}}
    </td>
    
    <td class="text CPatient-view" colspan="2" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if $canPlanningOp->read}}
        <div style="float: right;">
          {{if "web100T"|module_active}}
            {{mb_include module=web100T template=inc_button_iframe}}
          {{/if}}
          
          {{foreach from=$_sejour->_ref_operations item=curr_op}}
          <a class="action" title="Imprimer la DHE de l'intervention" href="#1" onclick="Admissions.printDHE('operation_id', {{$curr_op->_id}}); return false;">
            <img src="images/icons/print.png" />
          </a>
          {{foreachelse}}
          <a class="action" title="Imprimer la DHE du séjour" href="#1" onclick="Admissions.printDHE('sejour_id', {{$_sejour->_id}}); return false;">
            <img src="images/icons/print.png" />
          </a>
          {{/foreach}}
          
          <a class="action" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$_sejour->_id}}">
            <img src="images/icons/planning.png" title="{{tr}}Edit{{/tr}}" />
          </a>
          
          {{mb_include module=system template=inc_object_notes object=$_sejour}}
        </div>
      {{/if}}
      
      <input type="checkbox" name="print_doc" value="{{$_sejour->_id}}"/>
      
      {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
      
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_ref_patient->_guid}}');">
        {{$_sejour->_ref_patient->_view}}
      </span>
    </td>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
    </td>
    <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
        {{if ($_sejour->sortie_prevue < $date_min) || ($_sejour->sortie_prevue > $date_max)}}
          {{$_sejour->sortie_prevue|date_format:$conf.datetime}}
        {{else}}
          {{$_sejour->sortie_prevue|date_format:$conf.time}}
        {{/if}}
      </span>
      {{if $_sejour->confirme}}
        <img src="images/icons/tick.png" title="Sortie confirmée par le praticien" />
      {{/if}}
        
    </td>
    <td class="text" style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{if !($_sejour->type == 'exte') && !($_sejour->type == 'consult') && $_sejour->annule != 1}}
        {{if $conf.dPadmissions.show_prestations_sorties}}
          {{mb_include template=inc_form_prestations sejour=$_sejour edit=$canAdmissions->edit}}
        {{/if}}        

        {{foreach from=$_sejour->_ref_affectations item=_aff}}
          <div {{if $_aff->effectue}} class="effectue" {{/if}}> 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_aff->_guid}}');">
              {{$_aff->_ref_lit}}
            </span>
          </div>
        {{foreachelse}}
          <div class="empty">Non placé</div>
        {{/foreach}}
       {{/if}}  
    </td>
    {{if $conf.dPadmissions.show_dh}}
      <td style="background: {{$background}}; {{if !$_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
        {{foreach from=$_sejour->_ref_operations item=curr_op}}
        {{if $curr_op->_ref_actes_ccam|@count}}
        <span style="color: #484;">
        {{foreach from=$curr_op->_ref_actes_ccam item=_acte}}
          {{if $_acte->montant_depassement}}
            {{if $_acte->code_activite == 1}}
            Chir :
            {{elseif $_acte->code_activite == 4}}
            Anesth :
            {{else}}
            Activité {{$_acte->code_activite}} :
            {{/if}}
            {{mb_value object=$_acte field=montant_depassement}}
            <br />
          {{/if}}
        {{/foreach}}
        </span>
        {{/if}}
        {{if $curr_op->depassement}}
        <!-- Pas de possibilité d'imprimer les dépassements pour l'instant -->
        <!-- <a href="#" onclick="printDepassement({{$_sejour->sejour_id}})"></a> -->
        Prévu : {{mb_value object=$curr_op field="depassement"}}
        <br />
        {{/if}}
        {{foreachelse}}
        -
        {{/foreach}}
      </td>
    {{/if}}
  </tr>
  {{/foreach}}
</table>