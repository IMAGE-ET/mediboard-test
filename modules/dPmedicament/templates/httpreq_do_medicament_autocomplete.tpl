{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$produits item=produit}}
    <li style="text-align: left;">
      <small style="display: none;">{{$produit->CodeCIP}}</small>
			Produit:
			<strong> 
			{{if $search_libelle_long}}
				{{$produit->LibelleLong|replace:$needle:"<em>$needle</em>"}}
			{{else}}
			  {{$produit->Libelle|replace:$needle:"<em>$needle</em>"}}
			{{/if}}
			</strong>
			
			{{if $produit->Commentaire}}
			<div style="padding-left: 1em">
			  ({{$produit->Commentaire|replace:$needle:"<em>$needle</em>"}})
			</div>
			{{/if}}
    </li>
  {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>