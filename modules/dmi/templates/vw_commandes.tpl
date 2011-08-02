{{mb_script module=dPprescription script=prescription}}

<script type="text/javascript">
  function orderProduct(form){
    return onSubmitFormAjax(form, {
      onComplete: function(){
        getForm("filterCommandes").onsubmit();
      }
    });
  }
  
  function printOrdonnance(prescription_id, praticien_id, operation_id) {
    var url = new Url("dPprescription", "print_prescription");
    url.addParam("prescription_id", prescription_id);
    url.addParam("praticien_sortie_id", praticien_id);
    url.addParam("operation_id", operation_id);
    url.addParam("print", 1);
    url.addParam("only_dmi", 1);
    url.popup(800, 1000, "print_prescription");
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
      <th>Interv. après le</th>
      <td>
        {{mb_field object=$interv field="_date_min" prop="date" register=true form="filterCommandes"}}
      </td>
			
      <th>avant le</th>
      <td>
        {{mb_field object=$interv field="_date_max" prop="date" register=true form="filterCommandes"}}
      </td>
			
      <th>Type</th>
      <td>
        {{mb_field object=$dmi_line field="type" typeEnum="select" onchange="this.form.onsubmit()" emptyLabel="All"}}
      </td>
			
      <th>Afficher les lignes créées récemment</th>
      <td>
        <input type="checkbox" name="recent_lines" value="1" />
      </td>
			
      <td>
        <button type="submit" class="search">{{tr}}Filter{{/tr}}</button> (nb. de lignes de DMI limité à 300)
      </td>
    </tr>
  </table>
</form>

<div id="list-commandes"></div>
