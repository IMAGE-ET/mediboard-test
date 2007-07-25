
<script type="text/javascript">

var ViewFullPatient = {
  select: function(eLink) {
    // Select current row
    if (this.eCurrent) {
      Element.classNames(this.eCurrent).remove("selected");
    }
    this.eCurrent = eLink.parentNode.parentNode;
    Element.classNames(this.eCurrent).add("selected");
  },
  
  main: function() {
    PairEffect.initGroup("patientEffect", { 
      bStoreInCookie: true
    } );
  }
}
  
</script>


<table id="diagramme">
	<tr> 
		<th colspan=4>{{$sejour->_view}} <br/><br/> </th>
	</tr>
	<tr>
		{{if ($diagramme.bloc.type) != "none"}}
			<td class="only done ray"> ADMIS <br/> Date : {{$diagramme.admission.entree.date}}</td>
		{{else}}
			<td class="only"> ADMIS <br/> Date : {{$diagramme.admission.entree.date}}</td>
		{{/if}}
		<td> </td>
		{{if ($diagramme.bloc.type) != "none" && $diagramme.admission.sortie.reelle == "sortie_prevue"}}
			<td class="only expect ray"> SORTIE <br/> Date : {{$diagramme.admission.sortie.date}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>
		{{elseif ($diagramme.admission.sortie.reelle) == "sortie_reelle"}}
			<td class="only current"> SORTIE <br/> Date : {{$diagramme.admission.sortie.date}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>	
		{{else}}
			<td class="only"> SORTIE <br/> Date : {{$diagramme.admission.sortie.date}} <br/> Mode Sortie : {{$diagramme.admission.sortie.mode_sortie}}</td>
		{{/if}}
		<td> </td>
	</tr>
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	<tr>
		{{if ($diagramme.admission.sortie.reelle) == "sortie_reelle"}}
			<td class="only done ray" colspan=3> HOSPITALISÉ <br/> Chambre : {{$diagramme.hospitalise.chambre}}</td>
		{{else}}
			<td class="only current" colspan=3> HOSPITALISÉ <br/> Chambre : {{$diagramme.hospitalise.chambre}}</td>
		{{/if}}
		<td>
			Liste des affectations :
    	{{foreach from=$affectations item=curr_aff}}
    		{{if ($curr_aff->_id == $diagramme.hospitalise.affectation)}}	
    			<span class="listeCurrent">
    		{{else}}
    			<span>
    		{{/if}}
    		<br/>
	    	<a href="#"
	      	onmouseover="ObjectTooltip.create(this, 'CAffectation', {{$curr_aff->_id}})">
	      	Affecations du {{$curr_aff->entree|date_format:"%d/%m/%Y"}}
	      				 			au {{$curr_aff->sortie|date_format:"%d/%m/%Y"}}
	   	 	</a>
	   	 	</span>
   	 	{{/foreach}}
		</td>
	</tr>
	{{if ($diagramme.bloc.type) != "none"}}
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	<tr>
		{{if ($diagramme.bloc.sortieSalleReveil) != ""}}
			<td class="only done ray" colspan=3> AU BLOC <br/> 
				<a href="#"
	      	onmouseover="ObjectTooltip.create(this, 'COperation', {{$diagramme.bloc.id}})">
	      		Operation : {{$diagramme.bloc.vue}}
	   	 	</a>
			</td>
		{{else}}
			<td class="only current" colspan=3> AU BLOC <br/> 
				<a href="#"
	      	onmouseover="ObjectTooltip.create(this, 'COperation', {{$diagramme.bloc.id}})">
	      		Operation : {{$diagramme.bloc.vue}}
	   	 	</a>
	   	</td>
		{{/if}}
		<td>
			Liste des interventions :
    	{{foreach from=$sejour->_ref_operations item=curr_op}}
    		{{if ($curr_op->_id == $diagramme.bloc.id)}}	
    			<span class="listeCurrent">
    		{{else}}
    			<span>
    		{{/if}}
    		<br/>
	    	<a href="#"
	      	onmouseover="ObjectTooltip.create(this, 'COperation', {{$curr_op->_id}})">
	      	Intervention du {{$curr_op->_datetime|date_format:"%d/%m/%Y"}}
	   	 	</a>
	   	 	</span>
   	 	{{/foreach}}
		</td>
	</tr>
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	{{if ($diagramme.bloc.type) == "current"}}
	<tr>
		{{if ($diagramme.bloc.sortieSalle) == ""}}
			<td class="only current"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle}}</td>
		{{else}}
			<td class="only done ray"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle}}</td>
		{{/if}}	
		<td> </td>
		{{if ($diagramme.bloc.sortieSalleReveil) == ""}}
			<td class="only expect ray"> SORTIE SALLE DE RÉVEIL </td>
		{{else}}
			<td class="only done ray"> SORTIE SALLE DE RÉVEIL <br/> Heure : {{$diagramme.bloc.sortieSalleReveil}} </td>
		{{/if}}	
		<td> </td>
	</tr>
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	<tr>
		{{if ($diagramme.bloc.sortieSalle) == ""}}
			<td class="only expect ray"> SORTIE DE SALLE </td>
		{{elseif ($diagramme.bloc.salleReveil) != ""}}
			<td class="only done ray"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle}} </td>
		{{else}}
			<td class="only current"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle}} </td>
		{{/if}}
		<td class="right">  </td>
		{{if ($diagramme.bloc.salleReveil) == ""}}
			<td class="only expect ray"> EN SALLE DE RÉVEIL </td>
		{{elseif ($diagramme.bloc.sortieSalleReveil) != ""}}
			<td class="only done ray"> EN SALLE DE RÉVEIL <br/> Heure : {{$diagramme.bloc.salleReveil}} </td>
		{{else}}
			<td class="only current"> EN SALLE DE RÉVEIL <br/> Heure : {{$diagramme.bloc.salleReveil}} </td>
		{{/if}}
		<td> </td>
	</tr>
	{{elseif ($diagramme.bloc.type) == "done"}}
	<tr>
		<td class="only done ray"> EN SALLE <br/> Heure : {{$diagramme.bloc.salle}}</td>
		<td> </td>
		<td class="only done ray"> SORTIE SALLE DE RÉVEIL <br/> Heure : {{$diagramme.bloc.sortieSalleReveil}} </td>
		<td> </td>
	</tr>
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	<tr>
		<td class="only done ray"> SORTIE DE SALLE <br/> Heure : {{$diagramme.bloc.sortieSalle}} </td>
		<td class="right">  </td>
		<td class="only done ray"> EN SALLE DE RÉVEIL <br/> Heure : {{$diagramme.bloc.salleReveil}} </td>
		<td> </td>
	</tr>
	{{elseif ($diagramme.bloc.type) == "expect"}}
	<tr>
		<td class="only expect ray">  <br/> EN SALLE<br/><br/></td>
		<td> </td>
		<td class="only expect ray"> SORTIE SALLE DE RÉVEIL </td>
		<td> </td>
	</tr>
	<tr>
		<td class="arrowdown"> </td>
		<td> </td>
		<td class="arrowup"> </td>
		<td> </td>
	</tr>
	<tr>
		<td class="only expect ray"> <br/>SORTIE DE SALLE <br/><br/> </td>
		<td class="arrowright">  </td>
		<td class="only expect ray"> EN SALLE DE RÉVEIL </td>
		<td> </td>
	</tr>
	{{/if}}
	{{/if}}
</table>