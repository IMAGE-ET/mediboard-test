{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<td>
  {{foreach from=$affectations item=_affectation}}
    {{if $_affectation->_ref_service->radiologie == '1'}}
        {{$_affectation->entree|date_format:$conf.time}}<br/>
    {{/if}}
  {{/foreach}}
</td>
<td>
  {{foreach from=$affectations item=_affectation}}
    {{if $_affectation->_ref_service->radiologie == '1'}}
      {{if $_affectation->sortie !== $sortie}}{{$_affectation->sortie|date_format:$conf.time}}{{else}}-{{/if}}<br/>
    {{/if}}
  {{/foreach}}
</td>