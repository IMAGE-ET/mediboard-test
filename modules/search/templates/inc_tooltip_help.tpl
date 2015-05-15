{{*
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<!--Vue appellée lors du hover sur l'image d'aide (point d'interrogation) dans la recherche-->

{{mb_default var=display value=true}}
{{if $display}}
  <img src="style/mediboard/images/icons/help.png" alt="Aide"
       onmouseover="ObjectTooltip.createDOM(this, 'help-tooltip', {duration: 0})" />
  <table class="tbl" id="help-tooltip" style="display: none;">
    <tr>
      <th class="title" colspan="2">Recherche avec opérateurs</th>
    </tr>
    <tr>
      <th class="text" style="width: 300px;">
        Opérateur désiré
      </th>
      <th class="text" style="width: 300px;">
        Nomenclature
      </th>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        ET
      </td>
      <td class="text" style="width: 300px;">
        &&
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        OU
      </td>
      <td class="text" style="width: 300px;">
        ||
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        PAS
      </td>
      <td class="text" style="width: 300px;">
        !
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        Environ
      </td>
      <td class="text" style="width: 300px;">
        Mot~
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        Mot obligatoire
      </td>
      <td class="text" style="width: 300px;">
        +Mot
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        Mot interdit
      </td>
      <td class="text" style="width: 300px;">
        -Mot
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        Composé de
      </td>
      <td class="text" style="width: 300px;">
        *Mot ou M*t etc...
      </td>
    </tr>
    <tr>
      <td class="text" style="width: 300px;">
        Contenant
      </td>
      <td class="text" style="width: 300px;">
        ?ot ou M?t
      </td>
    </tr>
  </table>
{{/if}}
