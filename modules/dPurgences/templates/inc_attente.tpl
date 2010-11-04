{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$sejour->_veille}}
  {{assign var=rpu value=$sejour->_ref_rpu}}
  {{mb_include template=inc_icone_attente}}
  {{if $sejour->sortie_reelle}}
    <br />(sortie à {{$sejour->sortie_reelle|date_format:$dPconfig.time}})
  {{else}}
	{{mb_value object=$rpu field=_attente}}
	{{/if}}
	
{{else}}
  Admis la veille
{{/if}}
