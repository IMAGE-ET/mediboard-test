<script type="text/javascript">

function pageMain() {
  regRedirectPopupCal("{{$date}}", "?m={{$m}}&tab={{$tab}}&date=");
}

function submitRPU(oForm){
  oForm.submit();
}

</script>


<table style="width:100%">
  <tr>
  <th>
   le
   {{$date|date_format:"%A %d %B %Y"}}
   <img id="changeDate" src="./images/icons/calendar.gif" title="Choisir la date" alt="calendar" />
  </th>
  </tr>
  <tr>
    <td style="text-align: right">
     Type d'affichage
     <form name="selView" action="?m=dPurgences&amp;tab=vw_sortie_rpu" method="post">
	      <select name="selAffichage" onchange="submit();">
	        <option value="tous" {{if $selAffichage == "tous"}}selected = "selected"{{/if}}>Tous</option>
	        <option value="sortie" {{if $selAffichage == "sortie"}} selected = "selected" {{/if}}>Sortie à effectuer</option>
	      </select>
	    </form>
    </td>
  </tr>
</table>

<table class="tbl">
  <tr>
    <th>{{tr}}CRPU-_patient_id{{/tr}}</th>
    <th>{{tr}}CRPU-_responsable_id{{/tr}}</th>
    <th>
    {{mb_colonne class="CRPU" field="_prise_en_charge" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=vw_sortie_rpu"}}
    </th>
    <th>Sortie</th>
    
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  {{assign var="rpu" value=$curr_sejour->_ref_rpu}}
  <tr>
    <td>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_patient->_view}}
      </a>
    </td>
    <td>
      <a href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id={{$curr_sejour->_ref_rpu->_id}}">
        {{$curr_sejour->_ref_praticien->_view}}
      </a>
    </td>
    <td>
       <a href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$curr_sejour->_ref_rpu->_ref_consult->_id}}">Voir prise en charge</a><br />
       Praticien: {{$curr_sejour->_ref_rpu->_ref_consult->_ref_plageconsult->_ref_chir->_view}}
    </td>
    <td>
      <form name="editRPU-{{$rpu->_id}}" action="?m=dPurgences" method="post"> 
			  <input type="hidden" name="dosql" value="do_rpu_aed" />
			  <input type="hidden" name="m" value="dPurgences" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="rpu_id" value="{{$rpu->_id}}" />
			  <table>
				 <!-- Annulation de la sortie -->
			  {{if $rpu->sortie}}
			   <tr>
			     <td>
			       <input type="hidden" name="mode_sortie" value="" />
			       <input type="hidden" name="destination" value="" />
			       <input type="hidden" name="orientation" value="" />
			       <input type="hidden" name="sortie" value="" />
			       <button class="cancel" type="button" onclick="submitRPU(this.form)">
			         Annuler la sortie
			        </button>
			      </td>
			    </tr>
			  <!-- Sortie à effectuer -->
			  {{else}}
			   <tr>
			     <td class="text">
			      {{mb_field object=$rpu field="mode_sortie" defaultOption="&mdash; Mode de sortie"}}
			      {{mb_field object=$rpu field="destination" defaultOption="&mdash; Destination"}} 
			      {{mb_field object=$rpu field="orientation" defaultOption="&mdash; Orientation"}}
			      <input type="hidden" name="sortie" value="current" />
			      <button class="tick" type="button" onclick="submitRPU(this.form);">
			        Effectuer la sortie
			      </button>
			     </td>
			   </tr>
			  {{/if}}
			  </table>
			</form>
    </td>
  </tr>
  {{/foreach}}
</table>