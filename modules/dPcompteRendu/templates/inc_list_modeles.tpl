{{*
 * $Id$
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=special_names value="CCompteRendu"|static:"special_names"}}

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-owner', true);
  });
</script>

<ul id="tabs-owner" class="control_tabs">
  {{foreach from=$modeles key=owner item=_modeles}}
    <li>
      <a href="#owner-{{$owner}}" {{if !$_modeles|@count}}class="empty"{{/if}}>
        {{$owners.$owner}} <small>({{$_modeles|@count}})</small>
      </a>
    </li>
  {{/foreach}}
</ul>

{{foreach from=$modeles key=owner item=_modeles}}
  <div id="owner-{{$owner}}" style="display: none;">
    {{mb_include template=inc_modeles modeles=$modeles.$owner}}
  </div>
{{/foreach}}
