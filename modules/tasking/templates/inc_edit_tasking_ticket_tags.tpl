{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<td style="white-space: normal; text-align: left">
  <ul class="tags" id="tags">
    {{foreach from=$tasking_ticket->_ref_tags item=_tag}}
      <li data-tag_item_id="{{$_tag->_id}}" id="{{$_tag->_guid}}" style="background-color: #{{$_tag->color}}" class="tag">
        {{$_tag}}
        <button type="button" class="delete"
                onclick="Tasking.bindTag(form, $(this).up('li').get('tag_item_id'), 'removeTag');">
        </button>
      </li>
    {{/foreach}}
  </ul>
</td>