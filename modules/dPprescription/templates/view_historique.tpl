<table class="tbl">
  <tr>
    <th colspan="3" class="title">Historique</th>
  </tr>
  <tr>
    <th>Ligne</th>
    <th>Signature Prat</th>
    <th>Posologies</th>
  </tr>
  {{foreach from=$hist key=hist_line_id item=_hist_lines}}  
  {{assign var=line value=$lines.$hist_line_id}}
  <tr>
    <!-- Affichage du libelle du medicament -->
    <th colspan="3">{{$line->_view}}
    {{if $line->_traitement}}(Traitement Personnel){{/if}}
    </th>
  </tr>
  {{foreach from=$_hist_lines item=_line}}
  <tr>
    <td>Ligne prévue initialement du {{$_line->debut|date_format:"%d/%m/%Y"}} au {{$_line->_fin|date_format:"%d/%m/%Y"}}.
    {{if $_line->date_arret}}
    <br />
    Arrêt le {{$_line->date_arret|date_format:"%d/%m/%Y"}}
    {{/if}}</td>
    <td>
    {{if $_line->signee}}
    Oui
    {{else}}
    Non
    {{/if}}
    </td>
    <td>
    {{foreach from=$_line->_ref_prises item=prise name=foreach_prise}}
	    {{if $prise->quantite}}
	        {{$prise->_view}}
	      {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
	    {{/if}}
	  {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>