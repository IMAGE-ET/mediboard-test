{{*
 * $Id$
 *  
 * @category Maternité
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$fiches_anesth item=_fiche_anesth name=fiches_anesths}}
  {{$_fiche_anesth|smarty:nodefaults}}

  {{if !$smarty.foreach.fiches_anesths.last}}
    <hr style="border: 0; page-break-after: always;" />
  {{/if}}
{{/foreach}}