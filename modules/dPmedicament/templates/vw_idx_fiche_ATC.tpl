<table class="main">
  <tr>
    <!-- Affichage de la liste des fiches ATC -->
    <td>
	    <form name="newFicheATC" method="get" action="">
	      <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="tab" value="vw_idx_fiche_ATC" />
				<input type="hidden" name="fiche_ATC_id" value="0" />
				<input type="hidden" name="del" value="0" />
				<button type="button" class="new" onclick="this.form.submit();">Créer une nouvelle fiche</button>
			</form>
			
			<form name="delFicheATC" method="post" action="">
	      <input type="hidden" name="m" value="{{$m}}" />
	      <input type="hidden" name="dosql" value="do_fiche_ATC_aed" />
				<input type="hidden" name="fiche_ATC_id" value="" />
				<input type="hidden" name="del" value="1" />
			</form>
			
	    <table class="tbl">
			  <tr>
			    <th>Fiches ATC</th>
			  </tr>
			  {{foreach from=$fiches key=code_atc_1 item=_fiches_atc_1}}
			    {{assign var=libelle_atc_1 value=$code_to_libelle.$code_atc_1}}
			    <tr>
			      <th>{{$libelle_atc_1}}</th>
			    </tr>
			    {{foreach from=$_fiches_atc_1 key=code_atc_2 item=_fiches_atc_2}}
			      {{assign var=libelle_atc_2 value=$code_to_libelle.$code_atc_2}}
			      {{foreach from=$_fiches_atc_2 item=_fiche}}
			        <tr {{if $_fiche->_id == $fiche_ATC->_id}}class="selected"{{/if}}>
			          <td>
 			            <button style="float: right" type="button" class="trash notext" 
 			                    onclick="$V(document.delFicheATC.fiche_ATC_id, '{{$_fiche->_id}}');
   																 document.delFicheATC.submit();"></button>          																										
			            <a href="?m={{$m}}&amp;tab=vw_idx_fiche_ATC&amp;fiche_ATC_id={{$_fiche->_id}}">
			              {{$_fiche->code_ATC}} - {{$libelle_atc_2}} {{if $_fiche->libelle}}- {{$_fiche->libelle}}{{/if}}
			            </a>
								</td>
			        </tr>
				  	{{/foreach}} 
				  {{/foreach}}
			  {{/foreach}}
			</table>
		</td>
		<!-- Modification de la fiche ATC sélectionnée -->
		<td>
		  {{if $fiche_ATC->_id}}
		    <!-- Affichage de la fiche ATC -->
			  <form name="editFicheATC" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
			    <input type="hidden" name="m" value="dPmedicament" />
			    <input type="hidden" name="dosql" value="do_fiche_ATC_aed" />
			    <input type="hidden" name="del" value="0" />
			    <input type="hidden" name="fiche_ATC_id" value="{{$fiche_ATC->_id}}" />
			  
				  <table class="form">
				    <tr>
				      <th class="category" colspan="2">
				        Modification d'une fiche ATC
				      </th>
				    </tr>
						<tr>
						  <th>
						    {{mb_label object=$fiche_ATC field="code_ATC"}} 
						  </th>
						  <td>
						    {{mb_value object=$fiche_ATC field="code_ATC"}} -  {{$fiche_ATC->_libelle_ATC}}
						  </td>
						</tr>
						<tr>
						  <th>
						    {{mb_label object=$fiche_ATC field="libelle"}} 
						  </th>
						  <td>
						    {{mb_value object=$fiche_ATC field="libelle"}} 
						  </td>
						</tr>
						<tr>			 
						  <td style="height: 500px" colspan="2">{{mb_field object=$fiche_ATC field="description" id="htmlarea"}}</td>
						</tr>
				  </table>
			  </form>
		  {{else}}
			  <!-- Formulaire de création de fiche ATC -->
			  <form name="createFicheATC" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
			    <input type="hidden" name="m" value="dPmedicament" />
			    <input type="hidden" name="dosql" value="do_fiche_ATC_aed" />
			    <input type="hidden" name="del" value="0" />
			    <input type="hidden" name="fiche_ATC_id" value="" />
			  	
			  	<table class="form">
			  	  <tr>
			  	    <th colspan="2" class="category">Création d'une fiche ATC</th>
			  	  </tr>
			  	  <tr>
						  <th>
						    {{mb_label object=$fiche_ATC field="code_ATC"}} 
						  </th>
						  <td>
						    <select name="code_ATC" style="width: 200px;">
			  				{{foreach from=$classes_ATC key=libelle_ATC_1 item=classes_ATC_2}}
			  				  <optgroup label="{{$libelle_ATC_1}}">
			  				  {{foreach from=$classes_ATC_2 key=code_ATC_2 item=libelle_ATC_2}}
			  						<option value="{{$code_ATC_2}}">{{$code_ATC_2}}: {{$libelle_ATC_2}}</option>
			  				  {{/foreach}}
			  				  </optgroup>
			  				{{/foreach}}
			  	      </select>
						  </td>
						</tr>
						<tr>
						  <th>
						    {{mb_label object=$fiche_ATC field="libelle"}} 
						  </th>
						  <td>
						    {{mb_field object=$fiche_ATC field="libelle"}} 
						  </td>
						</tr>
						<tr>
						  <td colspan="2" style="text-align: center;">
						    <button type="button" class="submit" onclick="this.form.submit();">Créer</button>
						  </td>
						</tr>
				  </table>
			  </form>
		  {{/if}}
		</td>
	</tr>
</table>