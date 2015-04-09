{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th colspan="5" class="category">Messagerie interne</th>
</tr>

{{mb_include template=inc_pref spec=enum var=inputMode values="html|text"}}

<tr>
  <th colspan="5" class="category">Messagerie externe</th>
</tr>
{{mb_include template=inc_pref spec=bool var=ViewMailAsHtml}}

{{* en octet *}}
{{mb_include template=inc_pref spec=enum var=getAttachmentOnUpdate values="0|102400|204800|512000|1048576|2097152|5242880|10485760|52428800"}}
{{mb_include template=inc_pref spec=bool var=LinkAttachment}}
{{mb_include template=inc_pref spec=bool var=showImgInMail}}
{{mb_include template=inc_pref spec=enum var=nbMailList values="5|10|20|50|100|150"}}
{{mb_include template=inc_pref spec=bool var=markMailOnServerAsRead}}
{{mb_include template=inc_pref spec=bool var=mailReadOnServerGoToArchived}}
