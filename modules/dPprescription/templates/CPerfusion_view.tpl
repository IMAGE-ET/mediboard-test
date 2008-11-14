{{assign var="perf" value=$object}}
<table class="tbl">
  <tr>
    <th colspan="3">{{$perf->_view}}</th>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=type}} :
      {{mb_value object=$perf field=type}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_value object=$perf field=voie}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=vitesse}} :
      {{if $perf->vitesse}}
      {{mb_value object=$perf field=vitesse}} ml/h
      {{else}}
       - 
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=date_debut}} : 
      {{if $perf->date_debut}}
        {{mb_value object=$perf field=date_debut}} 
        {{if $perf->time_debut}}
          à {{mb_value object=$perf field=time_debut}}
        {{/if}}
      {{else}}
      -
      {{/if}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$perf field=duree}}
      {{mb_value object=$perf field=duree}} heures
    </td>
  </tr>
  <tr>
    <td>
      {{if $perf->_ref_lines|@count}}
      Produits :
      <ul>
        {{foreach from=$perf->_ref_lines item=_perf_line}}
          <li>{{$_perf_line->_view}}</li>
        {{/foreach}}
      </ul>
      {{else}}
      Aucun produit dans cette perfusion
      {{/if}}
    </td>
  </tr>
  {{if $perf->_ref_transmissions|@count}}
	  <tr>
	    <th colspan="3">Transmissions</th>
	  </tr>
	  {{foreach from=$perf->_ref_transmissions item=_transmission}}
	  <tr>
	    <td colspan="3">
	      {{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}
	    </td>
	  </tr>
	  {{/foreach}}
  {{/if}}
</table>