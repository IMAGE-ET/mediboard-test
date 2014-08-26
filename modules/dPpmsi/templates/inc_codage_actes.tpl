{{mb_default var=read_only value=false}}
{{mb_default var=show_ccam value=true}}
{{mb_default var=show_ngap value=true}}

{{assign var=obj_guid value=$subject->_guid}}

<table class="main layout">
  {{if $show_ccam}}
    <tr>
      <td id="codes_ccam_{{$obj_guid}}">
        <script>
          Main.add(function() {
            PMSI.reloadActesCCAM('{{$obj_guid}}', "{{$read_only}}");
          });
        </script>
      </td>
    </tr>
  {{/if}}
  {{if $show_ngap}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="20">Actes NGAP</th>
        </tr>
        <tr>
          <th class="category">{{mb_title class=CActeNGAP field=code}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=executant_id}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=quantite}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=montant_base}}</th>
          <th class="category">{{mb_title class=CActeNGAP field=montant_depassement}}</th>
        </tr>
        {{foreach from=$subject->_ref_actes_ngap item=acte_ngap}}
          <tr>
            <td class="button">{{mb_value object=$acte_ngap field=code}}</td>
            <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$acte_ngap->_ref_executant}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=quantite}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=montant_base}}</td>
            <td class="button">{{mb_value object=$acte_ngap field=montant_depassement}}</td>
          </tr>
          {{foreachelse}}
          <tr>
            <td class="empty" colspan="20">{{tr}}CActeNGAP.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/if}}
</table>