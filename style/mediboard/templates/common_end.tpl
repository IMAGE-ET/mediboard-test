<script>
  {{assign var=end_app value="CMbPerformance::end"|static_call:""}}

  // Send timing data in HTTP header
  {{assign var=timer value="CMbPerformance::out"|static_call:""}}
  {{mb_default var=dosql value=''}}

  // Perfomance log
  (function(){
    if (performance.timing) {
      var offset = (performance.timing.responseEnd - performance.timing.fetchStart);
      var pageDetail = {{$timer|@json}};
      MbPerformance.pageDetail = pageDetail;
      MbPerformance.log.defer("page", "{{$m}}|{{$dosql|ternary:$dosql:$a}}", pageDetail, 0, offset);
    }
  })();
</script>

</body>
</html>