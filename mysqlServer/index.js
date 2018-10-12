'use strict'
// Only keep a certain number of files

const Tail = require('tail').Tail;
var tail = new Tail("/var/lib/mysql/general.log");
tail.watch()
tail.on("line", data => {
	command=data.split("\t")[2];
	
  console.log(command);
	
});
