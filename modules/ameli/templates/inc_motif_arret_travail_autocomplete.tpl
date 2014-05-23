{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<ul>
  {{foreach from=$motifs item=_motif}}
      {{if $_motif->type == 'groupe'}}
        <li>
          <strong style="font-style: italic;">{{$_motif->libelle}}</strong>
        </li>
        {{foreach from=$_motif->_ref_children item=_child}}
          <li>
            <span class="motif" data-code="{{$_child->code}}" data-libelle="{{$_child->libelle}}">&dash; {{$_child->libelle}}</span>
          </li>
        {{/foreach}}
      {{elseif $_motif->type == 'motif'}}
        <li>
          {{if $_motif->_ref_group}}
            <strong style="font-style: italic;">{{$_motif->_ref_group->libelle}}</strong>
            <br/>
          {{/if}}
          <span class="motif" data-code="{{$_motif->code}}" data-libelle="{{$_motif->libelle}}">{{$_motif->libelle}}</span>
        </li>
      {{/if}}
  {{foreachelse}}
    <li>
      <i>{{tr}}No result{{/tr}}</i>
    </li>
  {{/foreach}}
</ul>
