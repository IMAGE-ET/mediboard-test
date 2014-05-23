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

<tr>
  <td style="text-align: left;" {{if !$critere->duree}}colspan="3"{{/if}}>
    {{section name=padding loop=$depth}}
      &emsp;
    {{/section}}
    {{if !$depth}}
      <strong>{{$critere->text}}</strong>
    {{else}}
      &bull; {{$critere->text}}
    {{/if}}
  </td>
  {{if $critere->duree}}
    <td style="text-align: center;">
      {{$critere->duree}} {{$critere->unite_duree}}
    </td>
    <td class="narrow">
      <input name="duree_indicative" type="radio" value="{{$critere->duree}}|{{$critere->unite_duree}}" onclick="Control.Modal.close();"/>
    </td>
  {{/if}}
</tr>

{{math assign=child_depth equation="x + 1" x=$depth}}
{{foreach from=$critere->_ref_children item=_critere}}

  {{mb_include module=ameli template=inc_critere_arret_travail critere=$_critere depth=$child_depth}}
{{/foreach}}