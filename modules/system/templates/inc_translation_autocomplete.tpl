{{*
  * 
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<ul>
  {{foreach from=$trad key=_key item=_trad}}
    <li id="autocomplete-{{$_key}}" data-string="{{$_key}}" data-locale="{{$_trad.val}}">
      {{$_trad.key|smarty:nodefaults}}<br/>
      <strong>{{$_trad.val|smarty:nodefaults}}</strong>
    </li>
  {{foreachelse}}
    <li>
      <span style="font-style: italic;">{{tr}}No result{{/tr}}</span>
    </li>
  {{/foreach}}
</ul>