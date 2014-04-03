{{*
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<div class="small-info">
  Le langage naturel est le langage de la parole nous l'utilisons quand nous parlons.
  Le langage informatique est ce qui sera inscrit dans la barre de recherche lorsque vous effectuez une recherche avancée.
</div>
<table class="main tbl">
  <tbody>
    <tr>
      <th class="narrow">Langage Naturel</th>
      <th class="narrow">Langage Informatique</th>
    </tr>
    <tr>
      <td>Je veux le mot 'Lettre' mais pas le mot 'Type dans le titre du document '</td>
      <td> title:Lettre NOT title:type</td>
    </tr>
  </tbody>
</table>