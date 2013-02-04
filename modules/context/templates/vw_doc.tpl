{{*
  * Documentation of the module
  *  
  * @category Context
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<h1>Documentation</h1>
<h2>Principe</h2>
<p>Ce module sert à obtenir une vue mediboard en fonction d'un contexte.</p>
<p>En fonction de paramètres définis dans la barre d'adresse (GET) le module va aiguiller mediboard sur la vue correspondante</p>

<h2>Paramètres</h2>
<table class="tbl" style="width:50%;">
  <tr>
    <th>Paramètre</th>
    <th>explication</th>
    <th>Note</th>
  </tr>
  <tr>
    <td>view</td>
    <td>Vue demandée au contexte, les valeurs possibles sont :
    <ul>
      <li>labo</li>
      <li>soins</li>
      <li>patient</li>
    </ul>
    </td>
    <td>
      Si aucune vue n'est donnée, une erreur est renvoyée
    </td>
  </tr>
  <tr>
    <td>ipp</td>
    <td>dentifiant Permanent du Patient</td>
    <td>N/A</td>
  </tr>
  <tr>
    <td>name</td>
    <td>Nom du patient. Dans le cas où vous n'avez pas l'IPP, une recherche est effectuée sur ce champ</td>
    <td>exemple : "dubois"</td>
  </tr>
</table>

<h2>Exemples</h2>
<a href="?m=context&amp;a=call&amp;view=patient&amp;ipp=12345">?m=context&amp;a=call&amp;view=patient&amp;ipp=12345</a> Donnera le dossier patient du patient ayant l'ipp 12345