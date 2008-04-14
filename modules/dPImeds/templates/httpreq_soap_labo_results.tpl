Code d'erreur : {{$results.ex}}<br />
{{$results.listeInfoLabo|@count}} Résultat(s) reçu(s)

<script language="Javascript" type="text/javascript">
  ImedsResultsWatcher.setResults({{$results|@JSON}});
</script>



<!--
<GetInfoLaboResponse xmlns="http://www.biodyssee.ch">
  <GetInfoLaboResult>int</GetInfoLaboResult>
  <listeInfoLabo>
    <InfoLabo>
      <NumSejour>string</NumSejour>
      <IsLabo>boolean</IsLabo>
      <IsLaboEntreDate>boolean</IsLaboEntreDate>
      <DateLaboDernier>dateTime</DateLaboDernier>
    </InfoLabo>
    <InfoLabo>
      <NumSejour>string</NumSejour>
      <IsLabo>boolean</IsLabo>
      <IsLaboEntreDate>boolean</IsLaboEntreDate>
      <DateLaboDernier>dateTime</DateLaboDernier>
    </InfoLabo>
  </listeInfoLabo>
  <ex>string</ex>
</GetInfoLaboResponse>

-->