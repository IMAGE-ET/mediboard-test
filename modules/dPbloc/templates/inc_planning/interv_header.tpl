{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Intervention -->
<th>Intervention</th>
<th>Coté</th>
<th>Anesthésie</th>
{{if !$_compact}}
  <th>Remarques</th>
  {{if $_materiel}}
    <th>Matériel</th>
  {{/if}}
{{else}}
  <th>Rques{{if $_materiel}} / Mat.{{/if}}</th>
{{/if}}
{{if $_extra}}
  <th>Extra</th>
{{/if}}
{{if $_duree}}
  <th>Durée</th>
{{/if}}
{{if $_by_prat}}
  <th>Salle</th>
{{/if}}