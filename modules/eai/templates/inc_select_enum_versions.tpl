{{*
 * Select versions for transformation rules
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{if $versions}}
  <select name="version">
    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
    {{foreach from=$versions item=_version}}
      <option value="{{$_version}}" {{if $transformation_rule->version == $_version}}selected{{/if}}>{{$_version}}</option>
    {{/foreach}}
  </select>
{{/if}}