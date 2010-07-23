{{mb_include_script module=dPprescription script=prescription}}

<script type="text/javascript">
  function orderProduct(form){
    return onSubmitFormAjax(form, {
      onComplete: function(){
        getForm("filterCommandes").onsubmit();
      }
    });
  }
  
  function printOrdonnance(prescription_id, praticien_id) {
    var url = new Url("dPprescription", "print_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("praticien_sortie_id", praticien_id);
    url.addParam("print", 1);
    url.popup(800, 600, "print_prescription");
  }
  
  Main.add(function(){
    getForm("filterCommandes").onsubmit();
  });
</script>

<form name="filterCommandes" action="?" method="get" onsubmit="return Url.update(this, 'list-commandes')">
  <input type="hidden" name="m" value="dmi" />
  <input type="hidden" name="a" value="httpreq_vw_list_commandes" />
  
  <table class="form">
    <tr>
      <th>Depuis le</th>
      <td>
        {{mb_field object=$dmi_line field="date" prop="date" register=true form="filterCommandes" onchange="this.form.onsubmit()"}}
      </td>
      <th>Type</th>
      <td>
        {{mb_field object=$dmi_line field="type" typeEnum="select" onchange="this.form.onsubmit()" emptyLabel="Tous les types"}}
      </td>
    </tr>
  </table>
</form>

<div id="list-commandes"></div>
