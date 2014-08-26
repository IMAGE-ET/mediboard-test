{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Patient -->
{{if $_show_identity}}
  <th>Nom - Prénom</th>
{{/if}}
<th>Age</th>
<th>Sexe</th>
{{if $_coordonnees}}
<th>Telephone</th>
{{/if}}