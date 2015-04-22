{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tr>
  <th class="title" {{if $show_duree_preop}}colspan="2"{{/if}}></th>
  {{assign var=suffixe value="_title"}}
  {{mb_include module=bloc template=inc_planning/$col1$suffixe}}
  {{mb_include module=bloc template=inc_planning/$col2$suffixe}}
  {{mb_include module=bloc template=inc_planning/$col3$suffixe}}
</tr>
<tr>
  {{assign var=suffixe value="_header"}}
  {{if $show_duree_preop}}<th>Heure US</th>{{/if}}
  <th>Heure</th>
  {{mb_include module=bloc template=inc_planning/$col1$suffixe}}
  {{mb_include module=bloc template=inc_planning/$col2$suffixe}}
  {{mb_include module=bloc template=inc_planning/$col3$suffixe}}
</tr>