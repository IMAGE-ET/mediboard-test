<form name="Edit-CFacture" action="#" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete : function() {Control.Modal.close();}});">
  {{mb_key    object=$facture}}
  {{mb_class  object=$facture}}
  <input type="hidden" name="del" value="0"/>
  <table class="form">
    <tr>
      <th colspan="2" class="category">
        Choisissez le statut de la facture {{$facture}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$facture field=definitive}}</th>
      <td>{{mb_field object=$facture field=definitive}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Print{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>