<table class="main">
  <tr>
    <th colspan=2>
	  Dr. {{$plage->_ref_chir->_view}}
	  <br />
	  {{$plage->date|date_format:"%A %d %B %Y"}}
	  <br />
	  {{$plage->_ref_salle->nom}} : {{$plage->debut|date_format:"%Hh%M"}} - {{$plage->fin|date_format:"%Hh%M"}}
	</th>
  </tr>
  
  {{if $getInstalledPpers}}
  <tr>
    <th class="title">Ajout de personnes</th><th class="title">Personnes en salle</th>
  </tr>
  <tr>
    <td>
      <table>
        <tr>
          <td>
            <!-- liste déroulante de choix de l'anesthesiste  et du personnel de bloc -->
            <form name="editPlage" action="index.php?m={{$m}}" method="post" onsubmit="return checkPlage()">
            <input type="hidden" name="m" value="dPbloc" />
            <input type="hidden" name="dosql" value="do_plagesop_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
            <input type="hidden" name="_repeat" value="1" />
            <input type="hidden" name="_type_repeat" value="1" />
          
            <select name="anesth_id">
            <option value="" />&mdash; Anesthésistes &mdash;</option>
            {{foreach from=$listAnesth item=_anesth}}
            <option value="{{$_anesth->_id}}" {{if $plage->anesth_id == $_anesth->_id}} selected="selected" {{/if}}>{{$_anesth->_view}}</option>
            {{/foreach}}
            </select>
            <button class="tick" type="submit">Modifier</button>
            </form>
            
          </td>
        </tr>
        <tr>
          <td>
            <form name="editAffectation" action="index.php?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
		    <input type="hidden" name="object_class" value="{{$plage->_class_name}}" />
		    <input type="hidden" name="realise" value="0" />
		    
		    
            <select name="user_id">
            <option value="" />&mdash; Personnel de bloc &mdash;</option>
            {{foreach from=$listPers item=_personnelBloc}}
            <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_view}}</option>
            {{/foreach}}
            </select>
            
            <button class="submit" type="submit">Ajouter en salle</button>
            </form>
          </td>
        </tr>
      </table>   
    </td>
    
    <td>
      <table class="tbl">
        <tr>
          <th>Anesthésiste : </th>
          {{if $plage->_ref_anesth->_view}}
            <td>{{$plage->_ref_anesth->_view}}</td>
          {{else}}
            <td>Aucun anesthésiste</td>
          {{/if}}
          
        </tr>
        <tr>
          <th>Personnel : </th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$personnels key=key item=_personnel}}
            <form name="editAffectation" action="index.php?m={{$m}}" method="post">
              <input type="hidden" name="m" value="dPpersonnel" />
              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affect_id" value="{{$key}}" />
              <input type="hidden" name="del" value="1" />
              <button class='cancel' type='submit'>{{$_personnel->_view}}</button>
            </form>
            
            {{/foreach}}
          </td>
        </tr>
      </table>
    </td> 
  </tr>
  {{/if}}
  <tr>
    <td width="50%">
	  <table class="tbl">
	    <tr>
		  <th colspan=3>
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
		    <a href="index.php?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
		    {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
		    </a>
		    </strong>
			<br />
			Admission le {{$curr_op->_ref_sejour->entree_prevue|date_format:"%a %d %b à %Hh%M"}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
            <br />
			Durée : {{$curr_op->temp_operation|date_format:"%Hh%M"}}
		  </td>
		  <td class="text">
		    <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
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
            <form name="editFrm{{$curr_op->operation_id}}" action="index.php" method="get">
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
		    <a href="index.php?m={{$m}}&amp;a=do_order_op&amp;cmd=insert&amp;id={{$curr_op->operation_id}}">
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
		    <a name="{{$curr_op->operation_id}}" style="float:right;" href="#" onclick="view_log('COperation',{{$curr_op->operation_id}})">
              <img src="images/icons/history.gif" alt="historique" />
            </a>
			<form name="editFrm{{$curr_op->operation_id}}" action="index.php" method="get">
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="a" value="do_order_op" />
            <input type="hidden" name="cmd" value="sethour" />
            <input type="hidden" name="id" value="{{$curr_op->operation_id}}" />
		    <strong>
		    <a href="index.php?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
		    {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
		    </a>
		    </strong>
			<br />
			Admission le {{$curr_op->_ref_sejour->entree_prevue|date_format:"%a %d %b à %Hh%M"}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
            <br />
			Horaire : {{$curr_op->time_operation|date_format:"%Hh%M"}} - Durée : {{$curr_op->temp_operation|date_format:"%Hh%M"}}
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
		    <a href="index.php?m=dPplanningOp&amp;tab=vw_edit_planning&amp;operation_id={{$curr_op->operation_id}}">
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
            <form name="editFrm{{$curr_op->operation_id}}" action="index.php" method="get">
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
		    <a href="index.php?m={{$m}}&amp;a=do_order_op&amp;cmd=up&amp;id={{$curr_op->operation_id}}">
		    <img src="images/icons/uparrow.png" width="12" height="12" alt="monter" border="0" />
			</a>
			<br />
			{{/if}}
			<a href="index.php?m={{$m}}&amp;a=do_order_op&amp;cmd=rm&amp;id={{$curr_op->operation_id}}">
		    <img src="images/icons/cross.png" width="12" height="12" alt="supprimer" border="0" />
			</a>
			{{if $curr_op->rank != $max}}
			<br />
		    <a href="index.php?m={{$m}}&amp;a=do_order_op&amp;cmd=down&amp;id={{$curr_op->operation_id}}">
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