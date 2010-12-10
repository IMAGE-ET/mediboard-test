{{* $Id: edit_prefs.tpl 10498 2010-10-27 18:23:40Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10498 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=modtab value="-"|explode:$value}}
{{if count($modtab) == 1}}
  {{tr}}module-{{$modtab.0}}-court{{/tr}}
{{else}}
  {{tr}}mod-{{$modtab.0}}-tab-{{$modtab.1}}{{/tr}}
{{/if}}