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

{{mb_script module="mediusers" script="color_selector" ajax=1}}

<script type="text/javascript">
  ColorSelector.init{{$var}} = function(form_name, color_view){
    this.sForm  = form_name;
    this.sColor = "pref[{{$var}}]";
    this.sColorView = color_view;
    this.pop();
  };
</script>

<input type="hidden" name="pref[{{$var}}]" value="{{$pref.user}}"/>
<button type="button" {{if $readonly}}disabled="disabled"{{/if}} onclick="ColorSelector.init{{$var}}(this.form, 'color_viewer_{{$var}}');">
  <span class="color-view" id="color_viewer_{{$var}}" style="{{if $pref.user}}background-color: #{{$pref.user}};{{/if}}"></span>Couleur
</button>
<button class="cancel notext" type="button" onclick="$V(getForm(this.form)['pref[{{$var}}]'], '' );">{{tr}}Cancel{{/tr}}</button>
