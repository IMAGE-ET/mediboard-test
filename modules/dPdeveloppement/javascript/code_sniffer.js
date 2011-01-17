CodeSniffer = {
	select: function(input) {
  	$$('.tree-header.selected').invoke('removeClassName', 'selected');
  	input.up('.tree-header').addClassName('selected');
  },

	run: function(button) {
		Console.trace('Run recursive file sniffing');
	},

  show: function(button) {
		var dir = $(button).up('.tree-content').id;
		var basename = $(button).up('.tree-header').down('.basename').textContent;
		var file = dir.replace(':', '/') + '/' + basename.strip();
		file = file.split('/').slice(1).join('/');
		new Url('dPdeveloppement', 'sniff_file') .
		  addParam("file", file) .
			requestModal(800, 600);
  },
}
