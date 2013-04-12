{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

{{mb_default var=onclick value=null}}
{{mb_default var=id value=null}}
{{mb_default var=title value=null}}
{{mb_default var=alt value=null}}
{{mb_default var=display_name value=false}}

<img {{if $onclick}}
       onclick="{{$onclick}}"
       style="cursor: pointer;"
     {{/if}}
     {{if $id}}id="{{$id}}" {{/if}}
     src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone|basename}}"
     {{if $title}} title="{{$title}}" {{/if}}
     {{if $alt}} alt="{{$alt}}" {{/if}}
/>
{{if $display_name}}
  {{$categorie|spancate}}
{{/if}}
