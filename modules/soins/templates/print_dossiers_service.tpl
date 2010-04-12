<table class="main tbl">
	{{if $service_id == "NP"}}
	    <tr>
	      <th class="title" colspan="6">
	      	<span style="float: right">
	      		{{$dateTime|date_format:$dPconfig.datetime}}
	      	</span>
					Patients non placés du {{$date|date_format:$dPconfig.date}}
				</th>
			</tr>
			<tr>
		    <th>{{mb_title class=CSejour field="patient_id"}}</th>
        <th>{{mb_title class=CSejour field="_entree"}}</th>
        <th>{{mb_title class=CSejour field="_sortie"}}</th>
        <th>{{mb_title class=CSejour field="praticien_id"}}</th>
        <th>{{mb_title class=CSejour field="type"}}</th>
        <th>{{mb_title class=CSejour field="libelle"}}</th>
			</tr>
		{{foreach from=$_sejours item=_sejour}}
	    {{mb_include module=soins template=inc_dossiers_service}}
		{{/foreach}}
	{{else}}
	  <tr>
      <th class="title" colspan="6">
        <span style="float: right">
          {{$dateTime|date_format:$dPconfig.datetime}}
        </span>
        {{$service->_view}} le {{$date|date_format:$dPconfig.date}}
      </th>
    </tr>
		<tr>
      <th>{{mb_title class=CSejour field="patient_id"}}</th>
      <th>{{mb_title class=CSejour field="_entree"}}</th>
      <th>{{mb_title class=CSejour field="_sortie"}}</th>
      <th>{{mb_title class=CSejour field="praticien_id"}}</th>
      <th>{{mb_title class=CSejour field="type"}}</th>
      <th>{{mb_title class=CSejour field="libelle"}}</th>
    </tr>
    {{foreach from=$service->_ref_chambres item=curr_chambre}}
      {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
        <tr>
          <th class="category" colspan="6" style="font-size: 0.9em; {{if !$curr_lit->_ref_affectations|@count}}opacity: 0.5;{{/if}}">
            <span style="float: left;">{{$curr_chambre}}</span>
            <span style="float: right;">{{$curr_lit->nom}}</span>
          </th>
        </tr> 
        {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
          {{assign var=_sejour value=$curr_affectation->_ref_sejour}}
		      {{mb_include module=soins template=inc_dossiers_service}}
				{{/foreach}}	
			{{/foreach}}
		{{/foreach}}
	{{/if}}
</table>


{{if $service_id == "NP"}}
  {{foreach from=$_sejours item=_sejour}}
    <div id="modal-{{$_sejour->_id}}" style="display: none; height: 600px; width: 950px; overflow: auto;">
      <div style="float: right"> 
        <button class="cancel" onclick="modalwindow.close();">Fermer</button>
      </div>
      
      <script type="text/javascript">
        Main.add(function () {
          if($('tab-{{$_sejour->_id}}')){
            Control.Tabs.create('tab-{{$_sejour->_id}}');
          }
        });
      </script>

      {{assign var=sejour_id value=$_sejour->_id}}                
      <ul id="tab-{{$sejour_id}}" class="control_tabs">
        <li><a href="#dossier-{{$sejour_id}}">Dossier complet</a></li>
        <li {{if !array_key_exists($sejour_id, $fiches_anesth)}}class="empty"{{/if}}><a href="#fiche_anesth-{{$sejour_id}}">Fiche d'anesthésie</a></li>
      </ul>
      <hr class="control_tabs" />

      <div id="dossier-{{$sejour_id}}" style="display: none; text-align: left;">
        {{$outputs.$sejour_id|smarty:nodefaults}}
      </div>
        
      <div id="fiche_anesth-{{$sejour_id}}" style="display: none; text-align: left;">
      {{if array_key_exists($sejour_id, $fiches_anesth)}}
      {{$fiches_anesth.$sejour_id|smarty:nodefaults}}  
      {{/if}}
      </div>
    </div>
  {{/foreach}}
{{else}}
  {{foreach from=$service->_ref_chambres item=curr_chambre}}
    {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
      {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
        {{assign var=_sejour value=$curr_affectation->_ref_sejour}}
		     <div id="modal-{{$_sejour->_id}}" style="display: none; height: 600px; width: 950px; overflow: auto;">
		      <div style="float: right"> 
		        <button class="cancel" onclick="modalwindow.close();">Fermer</button>
		      </div>
		      
		      <script type="text/javascript">
		        Main.add(function () {
		          if($('tab-{{$_sejour->_id}}')){
		            Control.Tabs.create('tab-{{$_sejour->_id}}', true);
		          }
		        });
		      </script>
		
		      {{assign var=sejour_id value=$_sejour->_id}}                
		      <ul id="tab-{{$sejour_id}}" class="control_tabs">
		        <li><a href="#dossier-{{$sejour_id}}">Dossier complet</a></li>
		        {{if array_key_exists($sejour_id, $fiches_anesth)}}
						<li><a href="#fiche_anesth-{{$sejour_id}}">Fiche d'anesthésie</a></li>
						{{/if}}
		      </ul>
		      <hr class="control_tabs" />
		
		      <div id="dossier-{{$sejour_id}}" style="display: none; text-align: left;">
		        {{$outputs.$sejour_id|smarty:nodefaults}}
		      </div>
		      
					{{if array_key_exists($sejour_id, $fiches_anesth)}}
	          <div id="fiche_anesth-{{$sejour_id}}" style="display: none; text-align: left;">
			        {{$fiches_anesth.$sejour_id|smarty:nodefaults}}  
			      </div>
					{{/if}}
		    </div>
      {{/foreach}}  
    {{/foreach}}
  {{/foreach}}
{{/if}}
	
	