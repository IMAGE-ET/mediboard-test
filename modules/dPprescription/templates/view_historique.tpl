<table class="tbl">
  <tr>
    <th colspan="3">Historique</th>
  </tr>
  <tr>
    <th>Ligne</th>
    <th>Signature Prat</th>
    <th>Posologies</th>
  </tr>
  {{foreach from=$parent_lines item=_line_parent}}
  <tr>
    <td>Ligne prévue initialement du {{$_line_parent->debut}} au {{$_line_parent->_fin}}.
    {{if $_line_parent->date_arret}}
    <br />
    Arrêt le {{$_line_parent->date_arret}}
    {{/if}}</td>
    <td>
    {{if $_line_parent->signee}}
    Oui
    {{else}}
    Non
    {{/if}}
    </td>
    <td>
    {{foreach from=$_line_parent->_ref_prises item=prise name=foreach_prise}}
	    {{if $prise->quantite}}
	        {{$prise->_view}}
	      {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
	    {{/if}}
	  {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
</table>