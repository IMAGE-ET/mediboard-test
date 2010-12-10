{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8692 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<select name="pref[{{$var}}]">
  {{if $user_id != "default"}} 
    <option value="">&mdash; {{tr}}Ditto{{/tr}}</option>
	{{/if}}

  <option value="0"{{if $pref.user == "0"}}selected="selected"{{/if}}>{{tr}}bool.0{{/tr}}</option>
  <option value="1"{{if $pref.user == "1"}}selected="selected"{{/if}}>{{tr}}bool.1{{/tr}}</option>
</select>
