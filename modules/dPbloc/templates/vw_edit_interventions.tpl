<table class="main">
  <tr>
    <th colspan=2>
      <form name="editPlageTiming" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_plagesop_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="temps_inter_op" value="{{$plage->temps_inter_op}}" />
        <input type="hidden" name="_repeat" value="1" />
        <input type="hidden" name="_type_repeat" value="1" />
	      Dr {{$plage->_ref_chir->_view}}
	      <br />
	      {{$plage->date|date_format:$dPconfig.longdate}}
	      <br />
	      {{$plage->_ref_salle->nom}}
	      :
        <select name="_heuredeb" class="notNull num">
        {{foreach from=$listHours item=heure}}
          <option value="{{$heure|string_format:"%02d"}}" {{if $plage->_heuredeb == $heure}} selected="selected" {{/if}} >
            {{$heure|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
        h
        <select name="_minutedeb">
        {{foreach from=$listMins item=minute}}
          <option value="{{$minute|string_format:"%02d"}}" {{if $plage->_minutedeb == $minute}} selected="selected" {{/if}} >
            {{$minute|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
	      à
        <select name="_heurefin" class="notNull num">
        {{foreach from=$listHours item=heure}}
          <option value="{{$heure|string_format:"%02d"}}" {{if $plage->_heurefin == $heure}} selected="selected" {{/if}} >
            {{$heure|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
        h
        <select name="_minutefin">
        {{foreach from=$listMins item=minute}}
          <option value="{{$minute|string_format:"%02d"}}" {{if $plage->_minutefin == $minute}} selected="selected" {{/if}} >
          {{$minute|string_format:"%02d"}}
          </option>
        {{/foreach}}
        </select>
        <button type="submit" class="tick notext">{{tr}}Save{{/tr}}</button>
	    </form>
    </th>
  </tr>
  
  <tr>
    <th class="title">Ajout de personnes</th><th class="title">Personnes en salle</th>
  </tr>
  <tr>
    <td>
      <table class="form">
        <tr>
          <td>
            <!-- liste déroulante de choix de l'anesthesiste  et du personnel de bloc -->
            <form name="editPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPbloc" />
            <input type="hidden" name="dosql" value="do_plagesop_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
            <input type="hidden" name="_repeat" value="1" />
            <input type="hidden" name="_type_repeat" value="1" />
          
            <select name="anesth_id">
            <option value="">&mdash; Anesthésiste</option>
            {{foreach from=$listAnesth item=_anesth}}
            <option value="{{$_anesth->_id}}" {{if $plage->anesth_id == $_anesth->_id}} selected="selected" {{/if}}>{{$_anesth->_view}}</option>
            {{/foreach}}
            </select>
            <button class="tick" type="submit">Modifier</button>
            </form>
            
          </td>
        </tr>

			  {{if $listPersAideOp}}
        <tr>
          <td>
            <form name="editAffectationAideOp" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
            <input type="hidden" name="object_class" value="{{$plage->_class_name}}" />
            <input type="hidden" name="realise" value="0" />
            <select name="personnel_id">
              <option value="">&mdash; Aide opératoire</option>
              {{foreach from=$listPersAideOp item=_personnelBloc}}
              <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
              {{/foreach}}
            </select>
            <button class="submit" type="submit">Ajouter personnel en salle</button>
            </form>
          </td>
        </tr>
        {{/if}}
        {{if $listPersPanseuse}}
        <tr>
          <td>
            <form name="editAffectationPanseuse" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
            <input type="hidden" name="object_class" value="{{$plage->_class_name}}" />
            <input type="hidden" name="realise" value="0" />
            <select name="personnel_id">
              <option value="">&mdash; Panseuse</option>
              {{foreach from=$listPersPanseuse item=_personnelBloc}}
              <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
              {{/foreach}}
            </select>
            <button class="submit" type="submit">Ajouter personnel en salle</button>
            </form>
          </td>
        </tr>
        {{/if}}
        
      </table>   
    </td>
    
    <td>
      <table class="tbl">
        <tr>
          <th>Anesthésiste</th>
          {{if $plage->_ref_anesth->_view}}
            <td>{{$plage->_ref_anesth->_view}}</td>
          {{else}}
            <td>Aucun anesthésiste</td>
          {{/if}}
        </tr>
        
        {{if $affectations_plage.op}}
        <tr>
          <th>Aide operatoire(s)</th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$affectations_plage.op item=_affectation}}
              <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPpersonnel" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
                <input type="hidden" name="del" value="1" />
                <button class='cancel' type='submit'>{{$_affectation->_ref_personnel->_ref_user->_view}}</button>
              </form>
            {{/foreach}}
          </td>
        </tr>
        {{/if}}
        {{if $affectations_plage.op_panseuse}}
        <tr>
          <th>Panseuse(s)</th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$affectations_plage.op_panseuse item=_affectation}}
              <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPpersonnel" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
                <input type="hidden" name="del" value="1" />
                <button class='cancel' type='submit'>{{$_affectation->_ref_personnel->_ref_user->_view}}</button>
              </form>
            {{/foreach}}
          </td>
        </tr>
        {{/if}}

      </table>
    </td> 
  </tr>

  <tr>
    <td width="50%">
	  <table class="tbl">
	    <tr>
		  <th colspan=3>
		    {{if $dPconfig.dPplanningOp.COperation.horaire_voulu}}
        <form name="editOrderVoulu" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPbloc" />
          <input type="hidden" name="dosql" value="do_order_voulu_op" />
          <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
          <input type="hidden" name="del" value="0" />
		      <button type="submit" class="tick" style="float: right;">Utiliser les horaires souhaités</button>
		    </form>
		    {{/if}}
		    Patients à placer
		  </th>
		</tr>
		{{foreach from=$list1 item=curr_op}}
		<tr>
		  <td width="50%">
		    <a style="float:right;" href="#" onclick="view_log('COperation',{{$curr_op->operation_id}})">
          <img src="images/icons/history.gif" alt="historique" />
        </a>
		    <strong>
		    <a href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
		    {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
		    </a>
		    </strong>
			  <br />
			  Admission le {{$curr_op->_ref_sejour->entree_prevue|date_format:"%a %d %b à %Hh%M"}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
        <br />
			  Durée : {{$curr_op->temp_operation|date_format:$dPconfig.time}}
			  {{if $curr_op->horaire_voulu}}
			  <br />
			  Horaire souhaité: {{$curr_op->horaire_voulu|date_format:$dPconfig.time}}
			  {{/if}}
			  {{if $listPlages|@count != '1'}}
			  <br />
			  <form name="changeSalle" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="rank" value="0" />
          <input type="hidden" name="operation_id" value="{{$curr_op->_id}}" />
          Changement de salle
			    <select name="plageop_id" onchange="this.form.submit()">
			      {{foreach from=$listPlages item="_plage"}}
			      <option value="{{$_plage->_id}}" {{if $plage->_id == $_plage->_id}} selected = "selected"{{/if}}>
			      {{$_plage->_ref_salle->nom}} / {{$_plage->debut|date_format:$dPconfig.time}} à {{$plage->fin|date_format:$dPconfig.time}}
			      </option>
			      {{/foreach}}
			    </select>
			  </form>
			  {{/if}}
		  </td>
		  <td class="text">
		    <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
          {{if $curr_op->libelle}}
            <em>[{{$curr_op->libelle}}]</em>
            <br />
          {{/if}}
          {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
          {{/foreach}}
        </a>
        {{if $curr_op->rques}}
          Remarques: {{$curr_op->rques|nl2br}}
          <br />
        {{/if}}
        Côté : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}
        <br />
        <form name="editFrmAnesth{{$curr_op->operation_id}}" action="?" method="get">
          <input type="hidden" name="m" value="{{$m}}" />
          <input type="hidden" name="a" value="do_order_op" />
          <input type="hidden" name="cmd" value="setanesth" />
          <input type="hidden" name="id" value="{{$curr_op->operation_id}}" />
          <select name="type" onchange="this.form.submit()">
            <option value="">&mdash; Anesthésie &mdash;</option>
            {{foreach from=$anesth item=curr_anesth}}
            <option value="{{$curr_anesth->type_anesth_id}}" {{if $curr_op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
              {{$curr_anesth->name}}
            </option>
            {{/foreach}}
          </select>
        </form>
		  </td>
		  <td>
		    {{if $curr_op->annulee}}
		    <img src="images/icons/cross.png" width="12" height="12" alt="annulée" border="0" />
		    {{else}}
		    <a href="?m={{$m}}&amp;a=do_order_op&amp;cmd=insert&amp;id={{$curr_op->operation_id}}">
		      <img src="images/icons/tick.png" width="12" height="12" alt="ajouter" border="0" />
			  </a>
			  {{/if}}
		  </td>
		</tr>
		{{/foreach}}
	  </table>
	</td>
	<td width="50%">
	  <table class="tbl">
	    <tr>
		  <th colspan=3>
		    Ordre des interventions
		  </th>
		</tr>
		{{foreach from=$list2 item=curr_op}}
		<tr>
		  <td width="50%">
		    <a id="op{{$curr_op->operation_id}}" style="float:right;" href="#nothing" onclick="view_log('COperation',{{$curr_op->operation_id}})">
          <img src="images/icons/history.gif" alt="historique" />
        </a>
			<form name="editFrmOrder{{$curr_op->operation_id}}" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="a" value="do_order_op" />
        <input type="hidden" name="cmd" value="sethour" />
        <input type="hidden" name="id" value="{{$curr_op->operation_id}}" />
		    <strong>
		    <a href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
		    {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
		    </a>
		    </strong>
			<br />
			Admission le {{$curr_op->_ref_sejour->entree_prevue|date_format:"%a %d %b à %Hh%M"}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
            <br />
			Horaire : {{$curr_op->time_operation|date_format:$dPconfig.time}} - Durée : {{$curr_op->temp_operation|date_format:$dPconfig.time}}
			{{if $curr_op->horaire_voulu}}
			<br />
			Horaire souhaité: {{$curr_op->horaire_voulu|date_format:$dPconfig.time}}
			{{/if}}
			<br />
			Pause : 
			<select name="hour">
			  <option selected="selected">{{$curr_op->pause|date_format:"%H"}}</option>
			  <option>00</option>
			  <option>01</option>
			  <option>02</option>
			  <option>03</option>
			  <option>04</option>
			</select>
			h
			<select name="min">
			  <option selected="selected">{{$curr_op->pause|date_format:"%M"}}</option>
			  <option>00</option>
			  <option>15</option>
			  <option>30</option>
			  <option>45</option>
			</select>
			<button class="tick" type="submit">Changer</button>
			</form>
		  </td>
		  <td class="text">
		    <a href="?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
            {{if $curr_op->libelle}}
              <em>[{{$curr_op->libelle}}]</em>
              <br />
            {{/if}}
            {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
            {{/foreach}}
            </a>
            {{if $curr_op->rques}}
            Remarques: {{$curr_op->rques|nl2br}}
            <br />
            {{/if}}
            Côté : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}
            <br />
            <form name="editAnesth{{$curr_op->operation_id}}" action="?" method="get">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="a" value="do_order_op" />
            <input type="hidden" name="cmd" value="setanesth" />
            <input type="hidden" name="id" value="{{$curr_op->operation_id}}" />
            <select name="type" onchange="this.form.submit()">
              <option value="">&mdash; Anesthésie &mdash;</option>
              {{foreach from=$anesth item=curr_anesth}}
              <option value="{{$curr_anesth->type_anesth_id}}" {{if $curr_op->type_anesth == $curr_anesth->type_anesth_id}} selected="selected" {{/if}} >
                {{$curr_anesth->name}}
              </option>
              {{/foreach}}
            </select>
            </form>
		  </td>
		  <td>
		    {{if $curr_op->rank != 1}}
		    <a href="?m={{$m}}&amp;a=do_order_op&amp;cmd=up&amp;id={{$curr_op->operation_id}}">
		    <img src="images/icons/uparrow.png" width="12" height="12" alt="monter" border="0" />
			</a>
			<br />
			{{/if}}
			<a href="?m={{$m}}&amp;a=do_order_op&amp;cmd=rm&amp;id={{$curr_op->operation_id}}">
		    <img src="images/icons/cross.png" width="12" height="12" alt="supprimer" border="0" />
			</a>
			{{if $curr_op->rank != $max}}
			<br />
		    <a href="?m={{$m}}&amp;a=do_order_op&amp;cmd=down&amp;id={{$curr_op->operation_id}}">
		    <img src="images/icons/downarrow.png" width="12" height="12" alt="descendre" border="0" />
			</a>
			{{/if}}
		  </td>
		</tr>
		{{/foreach}}
	  </table>
	</td>
  </tr>
</table>