{{*
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<input type="text" class="color_picker" name="pref[{{$var}}]" value="{{$pref.user}}"/>
<script>
  Main.add(function(){
    var _e = $("form-edit-preferences_pref[{{$var}}]");
    new jscolor.color(_e, {required:false})
  });
</script>
<button class="cancel notext" type="button" onclick="$V(getForm(this.form)['pref[{{$var}}]'], '' );">{{tr}}Cancel{{/tr}}</button>
