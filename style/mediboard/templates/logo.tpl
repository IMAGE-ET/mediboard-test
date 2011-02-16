{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=logo        value="images/pictures/logo.png"}}
{{assign var=logo_custom value="images/pictures/logo_custom.png"}}

{{if is_file($logo_custom)}}
  {{assign var=logo value=$logo_custom}}
{{/if}}
{{assign var=homepage value="-"|explode:$app->user_prefs.DEFMODULE}}
<a href="?m={{$homepage.0}}{{if $homepage|@count == 2}}&tab={{$homepage.1}}{{/if}}" name="Accueil">
<img src="{{$logo}}" 
     {{if @$width}}width="{{$width}}"{{/if}} 
     {{if @$height}}height="{{$height}}"{{/if}}
     {{if @$alt}}alt="{{$alt}}"{{/if}}
     {{if @$title}}title="{{$title}}"{{/if}}
     {{if @$class}}class="{{$class}}"{{/if}}
     {{if @$id}}id="{{$id}}"{{/if}} />
</a>
