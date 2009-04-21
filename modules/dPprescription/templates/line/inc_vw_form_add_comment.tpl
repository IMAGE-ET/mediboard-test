{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->_can_modify_comment}}
	{{mb_label object=$line field="commentaire"}}: 
	<input type="text" name="commentaire" size="60" value="{{$line->commentaire}}" 
         onchange="testPharma({{$line->_id}}); 
                   {{if $line->_class_name == 'CPrescriptionLineMedicament' && $line->substitute_for_id && !$line->substitution_active}}submitEditCommentaireSubst('{{$line->_id}}',this.value);{{else}}submitAddComment('{{$line->_class_name}}', '{{$line->_id}}', this.value);{{/if}}" />
{{else}}
  {{if $line->commentaire}}
    {{mb_label object=$line field="commentaire"}}: {{$line->commentaire}}
	{{else}}
	  Aucun commentaire
	{{/if}}
{{/if}}