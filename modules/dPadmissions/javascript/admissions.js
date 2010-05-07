Admissions = {
  filter: function(input) {
		var table = $("admissions");
		table.select("tr").invoke("show");
	  
	  var term = $V(input);
	  if (!term) return;
	  
	  table.select(".CPatient-view").each(function(e) {
	    if (!e.innerHTML.like(term)) {
	      e.up("tr").hide();
	    }
	  });
	}
}
