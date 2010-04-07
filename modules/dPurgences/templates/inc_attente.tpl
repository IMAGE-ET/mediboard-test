{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $sejour->sortie_reelle}}
  {{mb_value object=$sejour->_ref_rpu field=_attente}}
  <br />(sortie à {{$sejour->sortie_reelle|date_format:$dPconfig.time}})
{{else}}
  Admis la veille
{{/if}}
