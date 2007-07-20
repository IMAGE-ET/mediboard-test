<table class="tbl">
  <tr>
    <th class="title" colspan="5">Sortie hospi complètes</th>
  </tr>
  <tr>
    <th>Effectuer la sortie</th>
    <th>Patient</th>
    <th>Sortie prévue</th>
    <th>Praticien</th>
    <th>Chambre</th>
  </tr>
  {{foreach from=$listComp item=curr_sortie}}
  <tr>
    <td>
      <form name="editFrm{{$curr_sortie->affectation_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_affectation_aed" />
      <input type="hidden" name="affectation_id" value="{{$curr_sortie->affectation_id}}" />
      
      {{if $curr_sortie->effectue}}
      <input type="hidden" name="effectue" value="0" />
      <button class="cancel" type="button" onclick="submitComp(this.form)">
        Annuler la sortie
      </button>
      <br />
      {{$curr_sortie->_ref_sejour->sortie_reelle|date_format:"%H h %M"}} / 
      {{tr}}CAffectation._mode_sortie.{{$curr_sortie->_ref_sejour->mode_sortie}}{{/tr}}
      {{else}}
      <input type="hidden" name="effectue" value="1" />
      <button class="tick" type="button" onclick="submitComp(this.form)">
        Effectuer la sortie
      </button><br />
      {{mb_field object=$curr_sortie field="_mode_sortie"}}
      {{/if}}
      </form>
    </td>
    <td class="text">
    
		  <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sortie->_ref_sejour->_ref_patient->patient_id}}">
		    <img src="images/icons/edit.png" alt="modifier" />
		  </a>
      <b>{{$curr_sortie->_ref_sejour->_ref_patient->_view}}</b></td>
    <td>{{$curr_sortie->sortie|date_format:"%H h %M"}}</td>
    <td class="text">Dr. {{$curr_sortie->_ref_sejour->_ref_praticien->_view}}</td>
    <td class="text">{{$curr_sortie->_ref_lit->_view}}</td>
  </tr>
  {{/foreach}}
</table>