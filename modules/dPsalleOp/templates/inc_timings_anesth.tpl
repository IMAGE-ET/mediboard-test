       <tr id="one">
          <th class="category" style="vertical-align: middle">Timming</th>
          <td>
            <div id="timing">
            <form name="timing{{$selOp->operation_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
            <input type="hidden" name="del" value="0" />
          
            <table class="form">
              <tr>
                <td class="button">
                  {{if $selOp->entree_salle}}
                  Entrée patient:
                  {{if $can->edit}}
                  <input name="entree_salle" size="5" type="text" value="{{$selOp->entree_salle|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);submitAnesth(anesth{{$selOp->operation_id}});">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.entree_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="entree_salle" onchange="submitTiming(this.form);submitAnesth(anesth{{$selOp->operation_id}});">
                    <option value="">-</option>
                    {{foreach from=$timing.entree_salle|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->entree_salle}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.entree_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->entree_salle|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="entree_salle" value="" />
                  <button type="button" class="submit" onclick="this.form.entree_salle.value = 'current'; submitTiming(this.form);submitAnesth(anesth{{$selOp->operation_id}});">entrée patient</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->pose_garrot}}
                  Pose garrot:
                  {{if $can->edit}}
                  <input name="pose_garrot" size="5" type="text" value="{{$selOp->pose_garrot|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" style="padding-left: 20px;" onclick="this.form.pose_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="pose_garrot" onchange="submitTiming(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.pose_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->pose_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.pose_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->pose_garrot|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="pose_garrot" value="" />
                  <button type="button" class="submit" onclick="this.form.pose_garrot.value = 'current'; submitTiming(this.form);">pose garrot</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->debut_op}}
                  Début opération:
                  {{if $can->edit}}
                  <input name="debut_op" size="5" type="text" value="{{$selOp->debut_op|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.debut_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="debut_op" onchange="submitTiming(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.debut_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->debut_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.debut_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->debut_op|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="debut_op" value="" />
                  <button type="button" class="submit" onclick="this.form.debut_op.value = 'current'; submitTiming(this.form);">début intervention</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
              <tr>
                <td class="button">
                  {{if $selOp->sortie_salle}}
                  Sortie patient:
                  {{if $can->edit}}
                  <input name="sortie_salle" size="5" type="text" value="{{$selOp->sortie_salle|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.sortie_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="sortie_salle" onchange="submitTiming(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.sortie_salle|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->sortie_salle}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.sortie_salle.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->sortie_salle|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="sortie_salle" value="" />
                  <button type="button" class="submit" onclick="this.form.sortie_salle.value = 'current'; submitTiming(this.form);">sortie patient</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->retrait_garrot}}
                  Retrait garrot:
                  {{if $can->edit}}
                  <input name="retrait_garrot" size="5" type="text" value="{{$selOp->retrait_garrot|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.retrait_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="retrait_garrot" onchange="submitTiming(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.retrait_garrot|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->retrait_garrot}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.retrait_garrot.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->retrait_garrot|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="retrait_garrot" value="" />
                  <button type="button" class="submit" onclick="this.form.retrait_garrot.value = 'current'; submitTiming(this.form);">retrait garrot</button>
                  {{else}}-{{/if}}
                </td>
                <td class="button">
                  {{if $selOp->fin_op}}
                  Fin opération:
                  {{if $can->edit}}
                  <input name="fin_op" size="5" type="text" value="{{$selOp->fin_op|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitTiming(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.fin_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="fin_op" onchange="submitTiming(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.fin_op|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->fin_op}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.fin_op.value = ''; submitTiming(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->fin_op|date_format:"%Hh%M"}}
                  {{/if}}
                  
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="fin_op" value="" />
                  <button type="button" class="submit" onclick="this.form.fin_op.value = 'current'; submitTiming(this.form);">fin intervention</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
            </table>
          
          </form>
        </div>
        </td>
        </tr>
     
     <tbody id="two">
        <tr>
          <th rowspan="20" class="category" style="vertical-align: middle">Anesthésie</th>
            <td>
            
            <div id="anesth">
         
            <form name="anesth{{$selOp->operation_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPplanningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
            <input type="hidden" name="operation_id" value="{{$selOp->operation_id}}" />
            <input type="hidden" name="del" value="0" />
              
            <table>
              <tr>
                <td rowspan="2" style="vertical-align: middle;">
                  {{if $can->edit || $modif_operation}}
                  <select name="type_anesth" onchange="submitAnesth(this.form);">
                    <option value="">&mdash; Type d'anesthésie</option>
                    {{foreach from=$listAnesthType item=curr_anesth}}
                    <option value="{{$curr_anesth->type_anesth_id}}" {{if $selOp->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
                      {{$curr_anesth->name}}
                    </option>
                   {{/foreach}}
                  </select>
                  {{elseif $selOp->type_anesth}}
                    {{assign var="keyAnesth" value=$selOp->type_anesth}}
                    {{assign var="typeAnesth" value=$listAnesthType.$keyAnesth}}
                    {{$typeAnesth->name}}
                  {{else}}-{{/if}}
                  <br />par le Dr.
                  {{if $can->edit || $modif_operation}}
                  <select name="anesth_id" onchange="submitAnesth(this.form);">
                    <option value="">&mdash; Anesthésiste</option>
                    {{foreach from=$listAnesths item=curr_anesth}}
                    <option value="{{$curr_anesth->user_id}}" {{if $selOp->_ref_anesth->user_id == $curr_anesth->user_id}} selected="selected" {{/if}}>
                      {{$curr_anesth->_view}}
                    </option>
                    {{/foreach}}
                  </select>
                  {{elseif $selOp->_ref_anesth->user_id}}
                    {{assign var="keyChir" value=$selOp->_ref_anesth->user_id}}
                    {{assign var="typeChir" value=$listAnesths.$keyChir}}
                    {{$typeChir->_view}}
                  {{else}}-{{/if}}
                </td>
                <td>
                  {{if $selOp->induction_debut}}
                  Début d'induction:
                  {{if $can->edit}}
                  <input name="induction_debut" size="5" type="text" value="{{$selOp->induction_debut|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitAnesth(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.induction_debut.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="induction_debut" onchange="submitAnesth(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_debut|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_debut}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.induction_debut.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->induction_debut|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_debut" value="" />
                  <button type="button" class="submit" onclick="this.form.induction_debut.value = 'current'; submitAnesth(this.form);">Début d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr>
              <tr>
                <td>
                  {{if $selOp->induction_fin}}
                  Fin d'induction:
                  {{if $can->edit}}
                  <input name="induction_fin" size="5" type="text" value="{{$selOp->induction_fin|date_format:"%H:%M"}}" />
                  <button type="button" class="tick notext" onclick="submitAnesth(this.form);">{{tr}}Save{{/tr}}</button>
                  <button type="button" class="cancel notext" onclick="this.form.induction_fin.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{elseif $modif_operation}}
                  <select name="induction_fin" onchange="submitAnesth(this.form);">
                    <option value="">-</option>
                    {{foreach from=$timing.induction_fin|smarty:nodefaults item=curr_time}}
                    <option value="{{$curr_time}}" {{if $curr_time == $selOp->induction_fin}}selected="selected"{{/if}}>
                      {{$curr_time|date_format:"%Hh%M"}}
                    </option>
                    {{/foreach}}
                  </select>
                  <button type="button" class="cancel notext" onclick="this.form.induction_fin.value = ''; submitAnesth(this.form);">{{tr}}Cancel{{/tr}}</button>
                  {{else}}
                    {{$selOp->induction_fin|date_format:"%Hh%M"}}
                  {{/if}}
            
                  {{elseif $can->edit || $modif_operation}}
                  <input type="hidden" name="induction_fin" value="" />
                  <button type="button" class="submit" onclick="this.form.induction_fin.value = 'current'; submitAnesth(this.form);">Fin d'induction</button>
                  {{else}}-{{/if}}
                </td>
              </tr> 
            </table>
        </form>
      </div> <!-- Fin de la div pour le refresh -->  
     </td> 
    </tr> <!-- Fin de l'onglet  -->
    
    {{assign var="consult_anesth" value=$selOp->_ref_consult_anesth}}
    <!-- Affichage d'information complementaire pour l'anestesie -->
    <tr>
     <td>
     <table style="width: 100%">
     
    <tr>
		  <th colspan="3" class="category">Conditions d'intubation</th>
		</tr>
		<tr>
		  {{if $consult_anesth->mallampati}}
		  <td rowspan="4">
		    <img src="images/pictures/{{$consult_anesth->mallampati}}.gif" alt="{{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}" />
		    <br />Mallampati<br />de {{tr}}CConsultAnesth.mallampati.{{$consult_anesth->mallampati}}{{/tr}}
		   </td>
		   {{/if}}
			        
		   <th>Ouverture de la bouche</th>
		     <td>
		       {{tr}}CConsultAnesth.bouche.{{$consult_anesth->bouche}}{{/tr}}
		     </td>
		   </tr>
		   <tr>
		     <th>Distance thyro-mentonnière</th>
		     <td>{{tr}}CConsultAnesth.distThyro.{{$consult_anesth->distThyro}}{{/tr}}</td>
		   </tr>
			 <tr>
			   <th>Etat bucco-dentaire</th>
			   <td>{{$consult_anesth->etatBucco}}</td>
			 </tr>
			 <tr>
			   <th>Conclusion</th>
			   <td>{{$consult_anesth->conclusion}}</td>
			  </tr>
			  <tr>
			   {{if $consult_anesth->_intub_difficile}}
			    <td colspan="3"  style="text-align:center;color:#F00;">
			      Intubation Difficile Prévisible
			    </td>
			  {{else}}
			    <td colspan="3" style="text-align:center;">
			       Pas Intubation Difficile Prévisible
			     </td>        
			  {{/if}}
			  </tr>     
			  <tr>
          <th class="category" colspan="3">
            Examens Complémentaires
          </th>
        </tr>
        
        <tr>
        {{foreach from=$listChamps item=aChamps}}
          <td>
            {{if $aChamps}}
            <table>
            {{/if}}
            {{foreach from=$aChamps item=champ}}
              {{assign var="donnees" value=$unites.$champ}}
              <tr>
                <th>{{$donnees.nom}}</th>
                <td>
                  {{if $champ=="tca"}}
                    {{$consult_anesth->tca_temoin}} s / {{$consult_anesth->tca}}
                  {{elseif $champ=="tsivy"}}
                    {{$consult_anesth->tsivy|date_format:"%Mm%Ss"}}
                  {{elseif $champ=="ecbu"}}
                    {{tr}}CConsultAnesth.ecbu.{{$consult_anesth->ecbu}}{{/tr}}
                  {{else}}
                    {{$consult_anesth->$champ}}
                  {{/if}}
                  {{$donnees.unit}}
                </td>
              </tr>
            {{/foreach}}
            {{if $aChamps}}</table>{{/if}}
          </td>
        {{/foreach}}
        </tr>
           
        {{foreach from=$consult_anesth->_ref_consultation->_types_examen key=curr_type item=list_exams}}
        {{if $list_exams|@count}}
        <tr>
          <th>
            Examens Complémentaires : {{tr}}CExamComp.realisation.{{$curr_type}}{{/tr}}
          </th>
          <td class="text">
            <ul>
              {{foreach from=$list_exams item=curr_examcomp}}
              <li>
                {{$curr_examcomp->examen}}
                {{if $curr_examcomp->fait}}
                  (Fait)
                {{else}}
                  (A Faire)
                {{/if}}
              </li>
              {{/foreach}}
            </ul>
          </td>
        </tr>
       {{/if}}
       {{foreachelse}}
       <tr>
        <td>
          Pas d'examen complémentaire
        </td>
      </tr>
      {{/foreach}}
     
      
      {{if $consult_anesth->_ref_consultation->_ref_exampossum->_id}}
        <tr>
          <th>Score Possum</th>
          <td>
            Morbidité : {{mb_value object=$consult_anesth->_ref_consultation->_ref_exampossum field="_morbidite"}}%<br />
            Mortalité : {{mb_value object=$consult_anesth->_ref_consultation->_ref_exampossum field="_mortalite"}}%
          </td>
        </tr>
      {{/if}}
      
      {{if $consult_anesth->_ref_consultation->_ref_examnyha->_id}}
        <tr>
          <th>Clasification NYHA</th>
          <td>{{mb_value object=$consult_anesth->_ref_consultation->_ref_examnyha field="_classeNyha"}}</td>
        </tr>   
      {{/if}}
      
      {{if $consult_anesth->_ref_consultation->rques}}
        <tr>
          <th>
            Remarques
          </th>
          <td>
            {{$consult_anesth->_ref_consultation->rques|nl2br}}
          </td>
        </tr>
      {{/if}}
    
      <tr>
        <th class="category" colspan="3">Liste des Documents Edités</th>
      </tr>
      <tr>
        <td>
          {{if $consult_anesth->_ref_consultation->_ref_documents}}
          <ul>
          {{foreach from=$consult_anesth->_ref_consultation->_ref_documents item=currDoc}}
            <li>{{$currDoc->nom}}</li>
          {{/foreach}}
          </ul>
          {{else}}
            Aucun Document
          {{/if}}
        </td>
      </tr>
    
      {{if $consult_anesth->premedication}}
      <tr>
          <th class="category">
            Prémédication
          </th>
        </tr>
        <tr>
          <td>
            {{$consult_anesth->premedication|nl2br}}
          </td>
        </tr>
      {{/if}}
      {{if $consult_anesth->prepa_preop}}
        <tr>
          <th class="category">
            Préparation pré-opératoire
          </th>
        </tr>
        <tr>
          <td>
            {{$consult_anesth->prepa_preop|nl2br}}
          </td>
        </tr>
      {{/if}}
    </table>
    </td>
    </tr>
  </tbody>