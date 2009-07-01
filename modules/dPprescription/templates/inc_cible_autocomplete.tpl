{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
{{foreach from=$cibles item=cibles_by_type key=type}}
  {{foreach from=$cibles_by_type item=cible}}
    {{if $type == "cat"}}
   <li id="{{$cible->_id}}">
     <strong>{{tr}}CPrescription._chapitres.{{$cible->chapitre}}{{/tr}}</strong> :
     {{$cible->nom|lower|replace:$libelle_cible:"<em>$libelle_cible</em>"}}
   </li>
    {{else}}
    <li id="{{$cible.LIBELLE_CLASSE|lower}}" class="text">
     <strong>Classe ATC {{$cible.CODE_CLASSE}}</strong> :<br />
     {{$cible.LIBELLE_CLASSE|lower|replace:$libelle_cible:"<em>$libelle_cible</em>"}}
   </li>
    {{/if}}
  {{/foreach}}
{{/foreach}}
</ul>