{{* $Id: edit_prefs.tpl 10498 2010-10-27 18:23:40Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10498 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<select name="pref[{{$var}}]" class="text" size="1">
  {{if $user_id != "default"}} 
    <option value="">&mdash; {{tr}}Ditto{{/tr}}</option>
  {{/if}}

  {{foreach from=$modules item=_module}}
    {{assign var=mod_name value=$_module->mod_name}}
    <option value="{{$mod_name}}" {{if $mod_name == $pref.user}} selected="selected" {{/if}} style="font-weight: bold;">
      {{tr}}module-{{$mod_name}}-court{{/tr}}
    </option>
    
    {{foreach from=$_module->_tabs item=_tab}}
      <option value="{{$mod_name}}-{{$_tab}}" {{if "$mod_name-$_tab" == $pref.user}} selected="selected" {{/if}} style="padding-left: 1em;">
        {{tr}}mod-{{$_module->mod_name}}-tab-{{$_tab}}{{/tr}}
      </option>
    {{/foreach}}

  {{/foreach}}
</select>


