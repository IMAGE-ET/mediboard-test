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
	  {{assign var=code_cip value=$produit->CodeCIP}}
    <li style="text-align: left;">
      <small style="display: none;" class="code-cip">{{$produit->CodeCIP}}</small>
      <small style="display: none;" class="code-ucd">{{$produit->CodeUCD}}</small>
      <small style="display: none;" class="code-cis">{{$produit->code_cis}}</small>
			Produit:
			<strong>
			
			{{if $fast_access && $protocoles.$code_cip|@count}}
        <img src="images/icons/downarrow.png" style="float: right"/>
      {{/if}}
			
			{{if $search_libelle_long}}
				{{$produit->LibelleLong|emphasize:$tokens}}
			{{else}}
			  {{if $search_by_cis}}
			    {{$produit->ucd_view|emphasize:$tokens}}
			  {{else}}
			    {{$produit->Libelle|emphasize:$tokens}}
			  {{/if}}
			{{/if}}
			</strong>
			<br />
			{{* if !$search_libelle_long && $search_by_cis *}}
			<small>
				<span style="opacity: 0.5">
					{{if $produit->dci}}
					  DCI: {{$produit->dci|upper|emphasize:$tokens}} - 
					{{/if}}
					{{if $produit->CodeUCD || $produit->code_cis}}
					  {{$produit->forme_galenique}}
					{{/if}}
				</span>
			</small>
			{{* /if *}}
			{{if $produit->Commentaire}}
			<div style="padding-left: 1em">
			  ({{$produit->Commentaire|emphasize:$tokens}})
			</div>
			{{/if}}
      <small style="display: none;" class="libelle-long">{{$produit->LibelleLong}}</small>
      <small style="display: none;" class="libelle">{{$produit->Libelle}}</small>
		</li>
		{{if $fast_access && array_key_exists($code_cip, $protocoles)}}
			{{foreach from=$protocoles.$code_cip item=_protocole}}
			  <li style="text-align: left; padding-left: 10px;">
				  <small style="display: none;" class="protocole">{{$_protocole->_id}}</small>
      		<img src="images/icons/a_right.png" />
          {{$_protocole->libelle}}
					<br />
					<span style="opacity: 0.5; font-size: 0.8em; padding-left: 1em;">
        	{{foreach from=$_protocole->_counts_by_chapitre key=chapitre item=_count_chapitre name=chapitres}}
          {{if $_count_chapitre}}
            {{$_count_chapitre}} {{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}}{{if !$smarty.foreach.chapitres.last}}, {{/if}}
          {{/if}}
          {{/foreach}}
					</span>
				</li>
			{{/foreach}}
		{{/if}}
  {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>