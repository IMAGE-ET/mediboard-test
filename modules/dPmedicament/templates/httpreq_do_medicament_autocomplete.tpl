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
      <small style="display: none;">{{$produit->CodeUCD}}</small>
      <small style="display: none;">{{$produit->code_cis}}</small>
			Produit:
			<strong> 
			{{if $search_libelle_long}}
				{{$produit->LibelleLong|replace:$needle:"<em>$needle</em>"}}
			{{else}}
			  {{if $search_by_cis}}
			    {{$produit->ucd_view|replace:$needle:"<em>$needle</em>"}}
			  {{else}}
			    {{$produit->Libelle|replace:$needle:"<em>$needle</em>"}}
			  {{/if}}
			{{/if}}
			</strong>
			<br />
			{{if !$search_libelle_long && $search_by_cis}}
			<small><span style="opacity: 0.5">{{$produit->forme_galenique}}</span></small>
			{{/if}}
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