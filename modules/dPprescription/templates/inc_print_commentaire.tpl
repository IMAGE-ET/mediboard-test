{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<li>
	{{if !$comment->_protocole && !$praticien->_id}}
	  ({{$comment->_ref_praticien->_view}})
	{{/if}}
  {{$comment->commentaire|nl2br}}
</li>