{{*
 * $Id$
 *  
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org*}}
{{mb_script module=atih script=atih ajax=true}}
<form method="post" name="addSearchItem" class="watched prepared"  onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close.curry(),
onClose: atih.loadSearchItems.curry('{{$search_item->rss_id}}')
});">
  {{mb_key   object=$search_item}}
  {{mb_class object=$search_item}}
  <input type="hidden" name="del" value="0"/>
  {{mb_field object=$search_item field=rss_id hidden=true}}
  {{mb_field object=$search_item field=search_id hidden=true}}
  {{mb_field object=$search_item field=search_class hidden=true}}
  {{mb_field object=$search_item field=user_id value=$app->user_id hidden=true}}

  <table class="main form">
    <tr>
      <th class="title" colspan="2"> Formulaire d'ajout/édition de l'élement au RSS n° {{$search_item->rss_id}}</th>
    </tr>
    <tr>
      <td class="narrow">
        {{mb_label object=$search_item field=user_id}}
      </td>
      <td>
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=`$search_item->_ref_mediuser`}}
      </td>
    </tr>
    <tr>
      <td class="narrow">
        {{mb_label object=$search_item field=search_class}}
      </td>
      <td>
        {{tr}}{{mb_value object=$search_item field=search_class}}{{/tr}}
      </td>
    </tr>
    <tr>
      <td class="narrow">
        {{mb_label object=$search_item field=rmq}}
      </td>
      <td>
        {{mb_field object=$search_item field=rmq}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
        {{if $search_item->_id}}
          <button type="submit" class="trash" onclick="$V(this.form.del,'1')">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
