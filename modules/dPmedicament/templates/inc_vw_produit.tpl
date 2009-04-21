{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td>
    {{if !$produit->_supprime}}
      <img src="./images/icons/plus.gif" onclick="setClose('{{$produit->code_cip}}', '{{$line_id}}')" alt="Produit Hospitalier" title="Produit Hospitalier" />    
    {{/if}}
    {{$produit->code_cip}}
    {{if $produit->hospitalier}}
    <img src="./images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
    {{/if}}
    {{if $produit->_generique}}
    <img src="./images/icons/generiques.gif" alt="Produit Générique" title="Produit Générique" />
    {{/if}}
    {{if $produit->_referent}}
    <img src="./images/icons/referents.gif" alt="Produit Référent" title="Produit Référent" />
    {{/if}}
    {{if !$produit->inLivret}}
    <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
    {{/if}}
    {{if !$produit->inT2A}}
    <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
    {{/if}}  
  </td>
  <td>
    {{$produit->code_ucd}}
  </td>
  <td>
    <a href="#produit{{$produit->code_cip}}" onclick="viewProduit({{$produit->code_cip}})" {{if $produit->_supprime}}style="color: red"{{/if}}>{{$produit->libelle}}</a>
  </td>
  <td>
    {{$produit->nom_laboratoire}}
  </td>
</tr>