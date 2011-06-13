{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{$line->_ref_praticien->_view}}
{{if @$modules.messagerie}}
{{assign var=subject value="$line->_view - $sejour->_view"}}
<a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$subject}}')">
  <img src="images/icons/mbmail.png" title="Envoyer un message" />
</a>
{{/if}}
{{if $line->signee}}
  <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
{{else}}
  <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
{{/if}}