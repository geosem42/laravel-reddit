'use strict'
// Only keep a certain number of files
/*

CREATE EVENT `prune_general_log` ON SCHEDULE
EVERY 1 DAY STARTS '2013-10-18'
ON COMPLETION NOT PRESERVE
ENABLE
COMMENT 'This will trim the general_log table to contain only the past 24 hours of logs.'
DO BEGIN
  SET GLOBAL general_log = 'OFF';
  RENAME TABLE mysql.general_log TO mysql.general_log2;
  DELETE FROM mysql.general_log2 WHERE event_time &lt;= NOW()-INTERVAL 24 HOUR;
  OPTIMIZE TABLE general_log2;
  RENAME TABLE mysql.general_log2 TO mysql.general_log;
  SET GLOBAL general_log = 'ON';
END

CREATE EVENT 'prune_general_log' ON SCHEDULE EVERY 1 DAY STARTS '2013-10-18' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'This will trim the gene ral_log table to contain only the past 24 hours of logs.' DO BEGIN SET GLOBAL general_log = 'OFF';  RENAME TABLE mysql.general_log TO mysql.general_log2; DELETE FROM mysql.general_log2 WHERE event_time &lt;= NOW()-INTERVAL 24 HOUR; OPTIMIZE TABLE general_log2; RENAME TABLE mysql.general_log2 TO mysql.general_log; SET GLOBAL general_log = 'ON'; END;

*/


const Tail = require('tail').Tail;
var request=require("request");
var tail = new Tail("/var/lib/mysql/general.log");
tail.watch();
var writeCommands=["insert","replace","update"];
tail.on("line", data => {
	var command=data.split("\t")[2];
	if(writeCommands.indexOf(command)>-1){
		console.log(command);
	}
 
});
	
