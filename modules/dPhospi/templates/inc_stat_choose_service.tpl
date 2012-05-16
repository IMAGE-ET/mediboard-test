<script type="text/javascript">
  Main.add(function(){
    var form = getForm("filter_{{$type}}");
    Calendar.regField(form.date_min);
    Calendar.regField(form.date_max);
  });
</script>

{{mb_include module=hospi template=inc_form_stats type=$type}}

<div class="info">
  Veuillez choisir un service pour consulter les statistiques de taux d'occupation.
</div>
