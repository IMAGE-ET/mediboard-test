{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage messagerie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=bool var=ViewMailAsHtml}}
{{mb_include template=inc_pref spec=enum var=getAttachmentOnUpdate values="0|100|200|500|1000|2000|5000|10000|50000"}}
{{mb_include template=inc_pref spec=bool var=LinkAttachment}}
{{mb_include template=inc_pref spec=bool var=showImgInMail}}
{{mb_include template=inc_pref spec=enum var=nbMailList values="5|10|20|50|100|150"}}

