<table class="main">
  <tr>
    <th colspan="2">
      <form name="editPlageTiming" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_plagesop_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="temps_inter_op" value="{{$plage->temps_inter_op}}" />
        <input type="hidden" name="_repeat" value="1" />
        <input type="hidden" name="_type_repeat" value="1" />
        Dr {{$plage->_ref_chir->_view}}<br />
        {{$plage->date|date_format:"%A %d %B %Y"}}<br />
        {{$plage->_ref_salle->nom}} :
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
        �
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
    <th class="title">Ajout de personnes</th>
    <th class="title">Personnes en salle</th>
  </tr>
  <tr>
    <td>
      <table class="form">
        <tr>
          <td>
            <!-- liste d�roulante de choix de l'anesthesiste  et du personnel de bloc -->
            <form name="editPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPbloc" />
            <input type="hidden" name="dosql" value="do_plagesop_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
            <input type="hidden" name="_repeat" value="1" />
            <input type="hidden" name="_type_repeat" value="1" />
          
            <select name="anesth_id">
            <option value="">&mdash; Anesth�siste</option>
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
              <option value="">&mdash; Aide op�ratoire</option>
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
          <th>Anesth�siste</th>
          {{if $plage->_ref_anesth->_view}}
            <td>{{$plage->_ref_anesth->_view}}</td>
          {{else}}
            <td>Aucun anesth�siste</td>
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
    <td class="halfPane">
    <table class="tbl">
      <tr>
      <th colspan="3">
        {{if $dPconfig.dPplanningOp.COperation.horaire_voulu}}
        <form name="editOrderVoulu" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPbloc" />
          <input type="hidden" name="dosql" value="do_order_voulu_op" />
          <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
          <input type="hidden" name="del" value="0" />
          <button type="submit" class="tick" style="float: right;">Utiliser les horaires souhait�s</button>
        </form>
        {{/if}}
        Patients � placer
      </th>
    </tr>
    {{foreach from=$list1 item=curr_op}}
    <tr>
      <td style="width: 40%; white-space: nowrap;">
        <a style="float:right;" href="#" onclick="view_log('COperation',{{$curr_op->operation_id}})">
          <img src="images/icons/history.gif" alt="historique" />
        </a>
        <a style="font-weight: bold;" href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
          {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
        </a><br />
        Admission le {{mb_value object=$curr_op->_ref_sejour field=entree_prevue}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
        <br />
        Dur�e : {{$curr_op->temp_operation|date_format:"%Hh%M"}}
        {{if $curr_op->horaire_voulu}}
        <br />
        Horaire souhait�: {{$curr_op->horaire_voulu|date_format:"%Hh%M"}}
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
            {{$_plage->_ref_salle->nom}} / {{$_plage->debut|date_format:"%Hh%M"}} � {{$plage->fin|date_format:"%Hh%M"}}
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
        C�t� : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}
        <br />
        <form name="editFrmAnesth{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <select name="type_anesth" onchange="this.form.submit()">
            <option value="">&mdash; Anesth�sie &mdash;</option>
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
        <img src="images/icons/cross.png" width="12" height="12" alt="annul�e" border="0" />
        {{else}}
        <form name="edit-insert-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="_move" value="last" /><!-- Insertion � la fin -->
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <button type="submit" class="tick notext" title="{{tr}}Add{{/tr}}">{{tr}}Add{{/tr}}</button>
        </form>
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
    </table>
  </td>
  <td class="halfPane">
    <table class="tbl">
      <tr><th colspan="3">Ordre des interventions</th></tr>
    {{foreach from=$list2 item=curr_op}}
    <tr>
      <td style="width: 40%; white-space: nowrap;">
        <a id="op{{$curr_op->operation_id}}" style="float:right;" href="#nothing" onclick="view_log('COperation',{{$curr_op->operation_id}})">
          <img src="images/icons/history.gif" alt="historique" />
        </a>
        <form name="edit-pause-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <a style="font-weight: bold;" href="?m=dPpatients&amp;tab=vw_idx_patients&amp;patient_id={{$curr_op->_ref_sejour->_ref_patient->patient_id}}">
            {{$curr_op->_ref_sejour->_ref_patient->_view}} ({{$curr_op->_ref_sejour->_ref_patient->_age}} ans)
          </a><br />
          Admission le {{mb_value object=$curr_op->_ref_sejour field=entree_prevue}} ({{$curr_op->_ref_sejour->type|truncate:1:""|capitalize}})
            <br />
          Horaire : {{$curr_op->time_operation|date_format:"%Hh%M"}} - Dur�e : {{$curr_op->temp_operation|date_format:"%Hh%M"}}
          {{if $curr_op->horaire_voulu}}
          <br />
          Horaire souhait�: {{$curr_op->horaire_voulu|date_format:"%Hh%M"}}
          {{/if}}
          <br />
          Pause : 
          <select name="_pause_hour">
            <option selected="selected">{{$curr_op->pause|date_format:"%H"}}</option>
            <option>00</option>
            <option>01</option>
            <option>02</option>
            <option>03</option>
            <option>04</option>
          </select>
          h
          <select name="_pause_min">
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
            <em>[{{$curr_op->libelle}}]</em><br />
          {{/if}}
          {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
            <strong>{{$curr_code->code}}</strong> : {{$curr_code->libelleLong}}<br />
          {{/foreach}}
        </a>
        {{if $curr_op->rques}}
          Remarques: {{$curr_op->rques|nl2br}}<br />
        {{/if}}
        C�t� : {{tr}}COperation.cote.{{$curr_op->cote}}{{/tr}}<br />

        <form name="editFrmAnesth{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <select name="type_anesth" onchange="this.form.submit()">
            <option value="">&mdash; Anesth�sie &mdash;</option>
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
        <form name="edit-up-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="before" />
          <button type="submit" class="notext" title="{{tr}}Up{{/tr}}">
            <img src="images/icons/uparrow.png" width="12" height="12" alt="{{tr}}Up{{/tr}}" border="0" />
          </button>
        </form><br />
        {{/if}}
        <form name="edit-del-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="out" />
          <button type="submit" class="cancel notext" title="{{tr}}Delete{{/tr}}">{{tr}}Delete{{/tr}}</button>
        </form><br />
        {{if $curr_op->rank != $max}}
        <form name="edit-down-{{$curr_op->operation_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_planning_aed" />
          <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
          <input type="hidden" name="_move" value="after" />
          <button type="submit" class="notext" title="{{tr}}Down{{/tr}}">
            <img src="images/icons/downarrow.png" width="12" height="12" alt="{{tr}}Down{{/tr}}" border="0" />
          </button>
        </form><br />
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
    </table>
  </td>
  </tr>
</table>