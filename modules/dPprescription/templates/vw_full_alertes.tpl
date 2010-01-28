{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	Main.add( function(){
    Control.Tabs.create('tab_alertes', false);
	});
</script>

<ul class="control_tabs" id="tab_alertes">
	<li><a href="#interaction" {{if !$alertesInteractions|@count}}class="empty"{{/if}}>Interactions <small>({{$alertesInteractions|@count}})</small></a></li>
	<li><a href="#posologie" {{if !$alertesPosologie|@count}}class="empty"{{/if}}>Posologies <small>({{$alertesPosologie|@count}})</small></a></li>
  <li><a href="#profil" {{if !$alertesProfil|@count}}class="empty"{{/if}}>Profil <small>({{$alertesProfil|@count}})</small></a></li>
  <li><a href="#IPC" {{if !$alertesIPC|@count}}class="empty"{{/if}}>IPC <small>({{$alertesIPC|@count}}</small>)</a></li>
  <li><a href="#allergie" {{if !$alertesAllergies|@count}}class="empty"{{/if}}>Allergies <small>({{$alertesAllergies|@count}})</small></a></li>
</ul>
<hr class="control_tabs" />

<table class="tbl" id="interaction">
  <tr>
    <th colspan="5" class="title">{{$alertesInteractions|@count}} interaction(s)</th>
  </tr>
  <tr>
    <th>Gravité</th>
    <th>Mécanisme</th>
    <th>Conduite à tenir</th>
  </tr>
	{{foreach from=$interactions item=_interactions}}
	  {{assign var=produit1 value=$_interactions.CIP1}}
	  {{assign var=produit2 value=$_interactions.CIP2}}
	  <tr>
	    <th colspan="3">Interaction entre <strong>{{$produit1->libelle}}</strong> et <strong>{{$produit2->libelle}}</strong></th>
		</tr>	
		{{foreach from=$_interactions.interactions item=curr_alerte}}
		  <tr>
		    <td class="text">{{$curr_alerte->Gravite}}</td>
		    <td class="text">{{$curr_alerte->Type}}<br /><strong>{{$curr_alerte->strTexte}}</strong></td>
		    <td class="text">{{$curr_alerte->strConduite}}</td>
		  </tr>
		{{/foreach}}  
  {{foreachelse}}
  <tr>
  	<td colspan="3">Aucune interaction</td>
  </tr>
  {{/foreach}}
</table>

<table class="tbl" id="posologie" style="display: none;">
  <tr>
    <th colspan="3" class="title">{{$alertesPosologie|@count}} problème(s) de posologie</th>
  </tr>
  <tr>
    <th>Type</th>
    <th>Produit</th>
    <th>Problème</th>
  </tr>
  {{foreach from=$alertesPosologie item=curr_alerte}}
  <tr>
    <td class="text">
      {{if $curr_alerte->Type == "Qte"}}
        Quantité journalière
      {{else}}
        Durée de traitement
      {{/if}}
    </td>
    <td class="text">{{$curr_alerte->Produit}}</td>
    <td class="text">{{$curr_alerte->LibellePb}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3">Aucun problème de posologie</td>
  </tr>  
  {{/foreach}}
</table>


<table class="tbl" id="profil" style="display: none;">
  <tr>
    <th colspan="2" class="title">{{$alertesProfil|@count}} contre-indication(s) / précaution(s) d'emploi</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>CI/PE</th>
  </tr>
  {{foreach from=$alertesProfil item=curr_alerte}}
  <tr>
    <td class="text">{{$curr_alerte->Libelle}}</td>
    <td class="text">{{$curr_alerte->LibelleMot}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="2">Aucune contre-indication</td>
  </tr>	
	{{/foreach}}
</table>

<table class="tbl" id="IPC" style="display: none;">
  <tr>
    <th class="title">{{$alertesIPC|@count}} incompatibilité(s) pysico-chimiques</th>
  </tr>
</table>

<table class="tbl" id="allergie" style="display: none;">
  <tr>
    <th colspan="2" class="title">{{$alertesAllergies|@count}} hypersensibilité(s)</th>
  </tr>
  <tr>
    <th>Produit</th>
    <th>Allergie</th>
  </tr>
  {{foreach from=$alertesAllergies item=curr_alerte}}
  <tr>
    <td class="text">{{$curr_alerte->Libelle}}</td>
    <td class="text">{{$curr_alerte->LibelleAllergie}}</td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="2">Aucune allergie</td>
  </tr>  
  {{/foreach}}
</table>