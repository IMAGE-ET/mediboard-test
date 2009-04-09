<table class="tbl">
  <tr>
    <th {{if $type=="historique"}}colspan="3"{{else}}colspan="4"{{/if}} class="title">Historique {{if $type=="substitutions"}} - Substitutions{{/if}}</th>
  </tr>
  <tr>
    <th>Ligne</th>
    {{if $type == "historique"}}
    <th>Signature Prat</th>
    {{/if}}
    <th>Posologies</th>
    {{if $type == "substitutions"}}
    <th>Créé par</th>
    <th>Produit substitué</th>
    {{/if}}
  </tr>
  {{if array_key_exists('line', $hist)}}
	  {{foreach from=$hist.line key=hist_line_id item=_hist_lines}}  
	  {{assign var=line value=$lines.line.$hist_line_id}}
	  <tr>
	    <!-- Affichage du libelle du medicament -->
	    <th {{if $type=="historique"}}colspan="3"{{else}}colspan="4"{{/if}}>{{$line->_view}}
	    {{if $line->traitement_personnel}}(Traitement Personnel){{/if}}
	    </th>
	  </tr>
	  {{foreach from=$_hist_lines item=_line name="foreach_line"}}
	  <tr>
	    <td>Ligne prévue initialement du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->_fin|date_format:"%d/%m/%Y"}}.
	    {{if $_line->date_arret}}
	    <br />
	    Arrêt le {{$_line->date_arret|date_format:"%d/%m/%Y"}}
	    {{/if}}</td>
	    {{if $type ==  "historique"}}
		    <td>
		    {{if $_line->signee}}
		    Oui
		    {{else}}
		    Non
		    {{/if}}
		    </td>
	    {{/if}}
	    <td>
	    {{foreach from=$_line->_ref_prises item=prise name=foreach_prise}}
		    {{if $prise->quantite}}
		        {{$prise->_view}}
		      {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
		    {{/if}}
		  {{/foreach}}
	    </td>
	    {{if $type == "substitutions"}}
	    <td>{{$_line->_ref_creator->_view}}</td>
	    
	    <td>
	    {{if !$smarty.foreach.foreach_line.last}}
	    {{$_line->_ref_produit->libelle}}
	    {{else}}
	    Produit actuel
	    {{/if}}
	    
	    </td>
	    {{/if}}
	  </tr>
	  {{/foreach}}
	  {{/foreach}}
  {{/if}}
  {{if array_key_exists('perf', $hist)}}
	  {{foreach from=$hist.perf key=hist_line_id item=_hist_lines}}  
	  {{assign var=perf value=$lines.perf.$hist_line_id}}
	  <tr>
	    <!-- Affichage du libelle du medicament -->
	    <th colspan="3">{{$perf->_view}}</th>
	  </tr>
	  {{foreach from=$_hist_lines item=_perf name="foreach_line"}}
	  <tr>
	    <td>Perfusion prévue initialement du {{$_perf->_debut|date_format:"%d/%m/%Y"}} au {{$_perf->_date_fin|date_format:"%d/%m/%Y"}}.
	    {{if $_perf->date_arret}}
	    <br />
	    Arrêt le {{$_perf->date_arret|date_format:"%d/%m/%Y"}}
	    {{/if}}</td>
	    <td>
			  {{if $_perf->signature_prat}}
			    Oui
			  {{else}}
			    Non
			  {{/if}}
		  </td>
	    <td class="text">
	    {{$_perf->_view}}<br />
	    {{foreach from=$_perf->_ref_lines item=_perf_line name=foreach_perf_line}}
		    {{$_perf_line->_view}}
		    {{if !$smarty.foreach.foreach_perf_line.last}},{{/if}} 
		  {{/foreach}}
	    </td>
	  </tr>
	  {{/foreach}}
	  {{/foreach}}
  {{/if}}
</table>