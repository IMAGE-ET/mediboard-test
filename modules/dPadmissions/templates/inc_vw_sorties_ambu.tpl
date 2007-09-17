<table class="tbl">
  <tr>
    <th class="title" colspan="5">Sortie ambu</th>
  </tr>
  <tr>
    <th>Effectuer la sortie</th>
    <th>Patient</th>
    <th>Sortie prévue</th>
    <th>Praticien</th>
    <th>Chambre</th>
  </tr>
  {{foreach from=$listAmbu item=curr_sortie}}
  <tr>
    <td style="{{if !$curr_sortie->_ref_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      <form name="editFrm{{$curr_sortie->affectation_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="affectation_id" value="{{$curr_sortie->affectation_id}}" />
      {{if $curr_sortie->effectue}}
      <input type="hidden" name="effectue" value="0" />
      <button class="cancel" type="button" onclick="submitAmbu(this.form)">
        Annuler la sortie
      </button>
      <br />
    {{if ($curr_sortie->_ref_sejour->sortie_reelle < $date_min) || ($curr_sortie->_ref_sejour->sortie_reelle > $date_max)}}
      {{$curr_sortie->_ref_sejour->sortie_reelle|date_format:"%d/%m/%Y à %Hh%M"}}
    {{else}}
      {{$curr_sortie->_ref_sejour->sortie_reelle|date_format:"%H h %M"}}
    {{/if}}
      / 
      {{tr}}CAffectation._mode_sortie.{{$curr_sortie->_ref_sejour->mode_sortie}}{{/tr}} 
      {{else}}
      <input type="hidden" name="effectue" value="1" />
      <button class="tick" type="button" onclick="{{if (($date_actuelle > $curr_sortie->_ref_sejour->sortie_prevue) || ($date_demain < $curr_sortie->_ref_sejour->sortie_prevue))}}confirmationAmbu(this.form);{{else}}submitAmbu(this.form);{{/if}}">
        Effectuer la sortie
      </button>
      <br />      
      {{mb_field object=$curr_sortie field="_mode_sortie"}}

      {{/if}}
    
      </form>
    </td>
    <td class="text" style="{{if !$curr_sortie->_ref_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
		  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sortie->_ref_sejour->_ref_patient->patient_id}}">
		    <img src="images/icons/edit.png" alt="modifier" />
		  </a>
	  {{if $curr_sortie->_ref_sejour->_num_dossier}}[{{$curr_sortie->_ref_sejour->_num_dossier}}]{{/if}}
      <b>{{$curr_sortie->_ref_sejour->_ref_patient->_view}}</b>
      
    </td>
    <td style="{{if !$curr_sortie->_ref_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      {{$curr_sortie->sortie|date_format:"%H h %M"}}
      {{if $curr_sortie->confirme}}
         <img src="images/icons/tick.png" alt="Sortie confirmée par le praticien" />
      {{/if}}
    </td>
    <td class="text" style="{{if !$curr_sortie->_ref_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">Dr. {{$curr_sortie->_ref_sejour->_ref_praticien->_view}}</td>
    <td class="text" style="{{if !$curr_sortie->_ref_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">{{$curr_sortie->_ref_lit->_view}}</td>
  </tr>
  {{/foreach}}
  {{foreach from=$listSejourA item=curr_sejour}}
  <tr>
    <td style="{{if !$curr_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
      <form name="editFrm{{$curr_sejour->sejour_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_sejour_aed" />
      <input type="hidden" name="sejour_id" value="{{$curr_sejour->_id}}" />
      {{if $curr_sejour->sortie_reelle}}
      <input type="hidden" name="sortie_reelle" value="" />
      <button class="cancel" type="button" onclick="submitAmbu(this.form)">
        Annuler la sortie
      </button>
      <br />
      
    {{if ($curr_sejour->sortie_reelle < $date_min) || ($curr_sejour->sortie_reelle > $date_max)}}
      {{$curr_sejour->sortie_reelle|date_format:"%d/%m/%Y à %Hh%M"}}
    {{else}}
      {{$curr_sejour->sortie_reelle|date_format:"%H h %M"}} 
    {{/if}}
    / 
    {{tr}}CSejour.mode_sortie.{{$curr_sejour->mode_sortie}}{{/tr}} 
      {{else}}
      <input type="hidden" name="sortie_reelle" value="{{$date_sortie}}" />
     <button class="tick" type="button" onclick="{{if (($date_actuelle > $curr_sejour->sortie_prevue) || ($date_demain < $curr_sejour->sortie_prevue))}}confirmationAmbu(this.form);{{else}}submitAmbu(this.form);{{/if}}">
         Effectuer la sortie
      </button>
      <br />      
      {{mb_field object=$curr_sejour field="mode_sortie"}}
      
      {{/if}}
     
    
      </form>
    </td>
    <td class="text" style="{{if !$curr_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">
		  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sejour->_ref_patient->patient_id}}">
		    <img src="images/icons/edit.png" alt="modifier" />
		  </a>
      {{if $curr_sejour->_num_dossier}}[{{$curr_sejour->_num_dossier}}]{{/if}}
      <b>{{$curr_sejour->_ref_patient->_view}}</b>
    </td>
    <td style="{{if !$curr_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">{{$curr_sejour->sortie_prevue|date_format:"%H h %M"}}</td>
    <td class="text" style="{{if !$curr_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">Dr. {{$curr_sejour->_ref_praticien->_view}}</td>
    <td class="text" style="{{if !$curr_sejour->facturable}}background-image:url(images/icons/ray_vertical.gif); background-repeat:repeat;{{/if}}">Aucune chambre</td>
  </tr>
  {{/foreach}}
  
</table>