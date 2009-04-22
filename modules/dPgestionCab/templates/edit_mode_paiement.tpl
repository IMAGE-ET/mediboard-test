<table class="main">
  <tr>
    <!-- Mode de paiement --> 
    <td class="halfPane" rowspan="3">
      <a class="button new" href="?m=dPgestionCab&amp;tab=configure&amp;facture_id=0">
        Créer un nouveau mode de paiement
      </a>
      <table class="tbl">
        <tr>
          <th class="title" colspan="2">Mode de paiement</th>
        </tr>
         <tr>
          <th class="title">{{$etablissement}}</th>
        </tr>
        <tr>
          <th>Libellé</th>
        </tr>
        {{foreach from=$listModePaiementGroup item=_item}}     
        <tr {{if $_item->_id == $modePaiement->_id}}class="selected"{{/if}}>
          <td>
           <a href="?m=dPgestionCab&amp;tab=edit_mode_paiement&amp;mode_paiement_id={{$_item->_id}}" title="Modifier le mode de paiement">
              {{mb_value object=$_item field="nom"}}
            </a>
           </td>
        </tr>
        {{/foreach}}
        {{foreach from=$listModePaiementFonction key=keyModePaiement item=_itemModePaiement}}
        {{if $_itemModePaiement|@count}}
        <tr>
          <th class="title">{{$keyModePaiement}}</th>
        </tr>
         <tr>
          <th>Libellé</th>
        </tr>
        	{{foreach from=$_itemModePaiement item=_item}}
	        <tr {{if $_item->_id == $modePaiement->_id}}class="selected"{{/if}}>
	          <td>
	           <a href="?m=dPgestionCab&amp;tab=edit_mode_paiement&amp;mode_paiement_id={{$_item->_id}}" title="Modifier le mode de paiement">
	              {{mb_value object=$_item field="nom"}}
	            </a>
	           </td>
	        </tr>
        	{{/foreach}}
        {{/if}}
        {{/foreach}}
      </table>
  	</td>
  	
  	<!-- Opération sur le mode de paiement --> 
  	<td class="halfPane">
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	  <input type="hidden" name="dosql" value="do_modepaiement_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="mode_paiement_id" value="{{$modePaiement->_id}}" />
      <table class="form">
        <tr>
          {{if $modePaiement->_id}}
          <th class="title modify" colspan="2">
     	 	Modification du {{$modePaiement->_view}}
          </th>
          {{else}}
          <th class="title" colspan="2">
      		Création d'un nouveau mode de paiement
          </th>
          {{/if}}
        </tr>
        <tr>
          	<th>{{mb_label object=$modePaiement field="nom"}}</th>
            <td>{{mb_field object=$modePaiement field="nom"}} </td>
        </tr>
        <tr>	
          	<th>{{mb_label object=$modePaiement field="function_id"}}</th>
            <td>
            	<select name="function_id" class="{{$modePaiement->_props.function_id}}">
			        <option value="">&mdash; Associer à une fonction &mdash;</option>
			        {{foreach from=$listFunc item=curr_func}}
			          <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->function_id}}" {{if $curr_func->function_id == $modePaiement->function_id}} selected="selected" {{/if}}>
			            {{$curr_func->_view}}
			          </option>
			        {{/foreach}}
				</select>
            </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $modePaiement->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le mode de paiement',objName:'{{$modePaiement->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>       
      </table>
      </form>
    </td>
  </tr>
 </table>