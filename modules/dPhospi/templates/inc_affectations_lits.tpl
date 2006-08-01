          <tr class="lit" >
            <td>
              {{if $curr_lit->_overbooking}}
              <img src="modules/{{$m}}/images/warning.png" alt="warning" title="Over-booking: {{$curr_lit->_overbooking}} collisions" />
              {{/if}}
              {{$curr_lit->nom}}
            </td>
            <td class="action">
              {{if $canEdit}}
              <input type="radio" id="lit{{$curr_lit->lit_id}}" onclick="selectLit({{$curr_lit->lit_id}})" />
              {{/if}}
            </td>
          </tr>
          {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
          {{eval var=$curr_affectation->_ref_sejour->_ref_patient->_view assign="patient_view"}}
          <tr class="patient">
            {{if $curr_affectation->confirme}}
            <td class="text" style="background-image:url(modules/{{$m}}/images/ray.gif); background-repeat:repeat;">
            {{else}}
            <td class="text">
            {{/if}}
              {{if !$curr_affectation->_ref_sejour->entree_reelle || ($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}}
                <font style="color:#a33">
              {{else}}
                {{if $curr_affectation->_ref_sejour->septique == 1}}
                <font style="color:#3a3">
                {{else}}
                <font>
                {{/if}}
              {{/if}}
              
              {{if $curr_affectation->_ref_sejour->type == "ambu"}}
              <img src="modules/{{$m}}/images/X.png" alt="X" title="Sortant ce soir" />
              {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $demain}}
                {{if $curr_affectation->_ref_next->affectation_id}}
                <img src="modules/{{$m}}/images/OC.png" alt="OC" title="Sortant demain" />
                {{else}}
                <img src="modules/{{$m}}/images/O.png" alt="O" title="Sortant demain" />
                {{/if}}
              {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $date}}
                {{if $curr_affectation->_ref_next->affectation_id}}
                <img src="modules/{{$m}}/images/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
                {{else}}
                <img src="modules/{{$m}}/images/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
                {{/if}}
              {{/if}}
              {{if $curr_affectation->_ref_sejour->type == "ambu"}}
              <em>{{$patient_view}}</em>
              {{else}}
              <strong>{{$patient_view}}</strong>
              {{/if}}
              {{if (!$curr_affectation->_ref_sejour->entree_reelle) || ($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}}
              {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
              {{/if}}
            </font>
            </td>
            <td class="action" style="background:#{{$curr_affectation->_ref_sejour->_ref_praticien->_ref_function->color}}">
              {{$curr_affectation->_ref_sejour->_ref_praticien->_shortview}}
            </td>
          </tr>
          <tr class="dates">
            {{if $curr_affectation->_ref_prev->affectation_id}}
            <td class="text">
              <em>Déplacé</em> (chambre: {{$curr_affectation->_ref_prev->_ref_lit->_ref_chambre->nom}}):
              {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_entree_relative}} jours)
            </td>
            <td class="action">
              {{if $canEdit}}
              <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

              </form>
              
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient_view|addslashes}}'})">
                <img src="modules/{{$m}}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
              {{/if}}
            </td>
            {{else}}
            <td class="text">
              {{if $canEdit}}
              <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

              </form>
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient_view|addslashes}}'})">
                <img src="modules/{{$m}}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
              {{/if}}
              <em>Entrée</em>:
              {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_entree_relative}} jours)
            </td>
            <td class="action">
              {{if $canEdit}}
              <form name="entreeAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />

              </form>
              
              <a>
                <img id="entreeAffectation{{$curr_affectation->affectation_id}}__trigger_entree" src="modules/{{$m}}/images/planning.png" alt="Planning" title="Modifier la date d'entrée" />
              </a>
              {{/if}}
            </td>
            {{/if}}
          </tr>
          <tr class="dates">
            {{if $curr_affectation->_ref_next->affectation_id}}
            <td class="text" colspan="2">
              <em>Déplacé</em> (chambre: {{$curr_affectation->_ref_next->_ref_lit->_ref_chambre->nom}}):
              {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_sortie_relative}} jours)
            </td>
            {{else}}
            <td class="text">
              {{if $canEdit}}
              <form name="splitAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_split" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="sejour_id" value="{{$curr_affectation->sejour_id}}" />
              <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
              <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
              <input type="hidden" name="no_synchro" value="1" />
              <input type="hidden" name="_new_lit_id" value="" />
              <input type="hidden" name="_date_split" value="{{$curr_affectation->sortie}}" />

              </form>
              
              <a style="float: right;">
                <img id="splitAffectation{{$curr_affectation->affectation_id}}__trigger_split" src="modules/{{$m}}/images/move.gif" alt="Move" title="Déplacer un patient" />
              </a>
              {{/if}}

              <em>Sortie</em>:
              {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_sortie_relative}} jours)
            </td>
            <td class="action">
              {{if $canEdit}}
              <form name="sortieAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />

              </form>
              
              <a>
                <img id="sortieAffectation{{$curr_affectation->affectation_id}}__trigger_sortie" src="modules/{{$m}}/images/planning.png" alt="Planning" title="Modifier la date de sortie" />
              </a>
              {{/if}}
            </td>
            {{/if}}
          </tr>
          <tr class="dates">
            <td colspan="2"><em>Age</em>: {{$curr_affectation->_ref_sejour->_ref_patient->_age}} ans</td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2"><em>Dr. {{$curr_affectation->_ref_sejour->_ref_praticien->_view}}</em></td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              {{foreach from=$curr_affectation->_ref_sejour->_ref_operations item=curr_operation}}
                {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
                <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
                {{/foreach}}
              {{/foreach}}
            </td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              <form name="SeptieSejour{{$curr_affectation->_ref_sejour->sejour_id}}" action="?m=dPhospi" method="post">

              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="otherm" value="dPhospi" />
              <input type="hidden" name="dosql" value="do_sejour_aed" />
              <input type="hidden" name="sejour_id" value="{{$curr_affectation->_ref_sejour->sejour_id}}" />
        
              <em>Pathologie</em>:
              {{$curr_affectation->_ref_sejour->pathologie}}
              -
              {{if $canEdit}}
              <input type="radio" name="septique" value="0" {{if $curr_affectation->_ref_sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
              <label for="septique_0" title="Séjour propre">Propre</label>
              <input type="radio" name="septique" value="1" {{if $curr_affectation->_ref_sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
              <label for="septique_1" title="Séjour septique">Septique</label>
              {{else}}
                {{if $curr_affectation->_ref_sejour->septique == 0}}
                Propre
                {{else}}
                Septique
                {{/if}}
              {{/if}}
              </form>
                            
            </td>
          </tr>
          {{if $curr_affectation->_ref_sejour->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Séjour</em>: {{$curr_affectation->_ref_sejour->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          {{foreach from=$curr_affectation->_ref_sejour->_ref_operations item=curr_operation}}
          {{if $curr_operation->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Intervention</em>: {{$curr_operation->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          {{/foreach}}
          {{if $curr_affectation->_ref_sejour->_ref_patient->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Patient</em>: {{$curr_affectation->_ref_sejour->_ref_patient->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          <tr class="dates">
            <td class="text" colspan="2">
              {{if $canEdit}}
              <form name="editChFrm{{$curr_affectation->_ref_sejour->sejour_id}}" action="index.php" method="post">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="dosql" value="do_edit_chambre" />
              <input type="hidden" name="id" value="{{$curr_affectation->_ref_sejour->sejour_id}}" />
              {{if $curr_affectation->_ref_sejour->chambre_seule == 'o'}}
              <input type="hidden" name="value" value="n" />
              <button class="change" type="submit" style="background-color: #f55;">
                chambre simple
              </button>
              {{else}}
              <input type="hidden" name="value" value="o" />
              <button class="change" type="submit">
                chambre double
              </button>
              {{/if}}
              </form>
              {{/if}}
            </td>
          </tr>
          {{foreachelse}}
          <tr class="litdispo"><td colspan="2">Lit disponible</td></tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            depuis:
            {{if $curr_lit->_ref_last_dispo && $curr_lit->_ref_last_dispo->affectation_id}}
            {{$curr_lit->_ref_last_dispo->sortie|date_format:"%A %d %B %H:%M"}} 
            ({{$curr_lit->_ref_last_dispo->_sortie_relative}} jours)
            {{else}}
            Toujours
            {{/if}}
            </td>
          </tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            jusque: 
            {{if $curr_lit->_ref_next_dispo && $curr_lit->_ref_next_dispo->affectation_id}}
            {{$curr_lit->_ref_next_dispo->entree|date_format:"%A %d %B %H:%M"}}
            ({{$curr_lit->_ref_next_dispo->_entree_relative}} jours)
            {{else}}
            Toujours
            {{/if}}
            </td>
          </tr>
          {{/foreach}}