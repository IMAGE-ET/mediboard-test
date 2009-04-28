{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<select name="lang" style="float:right;" onchange="this.form.submit()">
  <option value="{{$cim10|const:'LANG_FR'}}" {{if $lang == $cim10|const:'LANG_FR'}}selected="selected"{{/if}}>
    Français
  </option>
  <option value="{{$cim10|const:'LANG_EN'}}" {{if $lang == $cim10|const:'LANG_EN'}}selected="selected"{{/if}}>
    English
  </option>
  <option value="{{$cim10|const:'LANG_DE'}}" {{if $lang == $cim10|const:'LANG_DE'}}selected="selected"{{/if}}>
    Deutsch
  </option>
</select>