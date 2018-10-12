const Tail = require('tail').Tail;
var tail = new Tail("/var/lib/mysql/general.log");
tail.watch()
tail.on("line", data => {
  console.log(data.split("/"));
	
});
