{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage 
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include template=inc_pref spec=enum var=saveOnPrint values="0|1|2"}}
{{mb_include template=inc_pref spec=enum var=choicepratcab values="prat|cab|group"}}
{{mb_include template=inc_pref spec=enum var=listDefault values="ulli|br|inline"}}
{{mb_include template=inc_pref spec=str  var=listBrPrefix}}
{{mb_include template=inc_pref spec=str  var=listInlineSeparator}}
{{mb_include template=inc_pref spec=bool var=aideTimestamp}}
{{mb_include template=inc_pref spec=bool var=aideOwner}}
{{mb_include template=inc_pref spec=bool var=aideFastMode}}
{{mb_include template=inc_pref spec=bool var=aideAutoComplete}}
{{mb_include template=inc_pref spec=bool var=aideShowOver}}
{{mb_include template=inc_pref spec=bool var=pdf_and_thumbs}}
{{mb_include template=inc_pref spec=bool var=mode_play}}
{{mb_include template=inc_pref spec=enum var=choice_factory values="CDomPDFConverter|CWkHtmlToPDFConverter"}}
{{mb_include template=inc_pref spec=bool var=multiple_docs}}
{{mb_include template=inc_pref spec=bool var=auto_capitalize}}
