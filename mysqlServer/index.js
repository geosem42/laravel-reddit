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

CREATE EVENT 'prune_general_log' ON SCHEDULE EVERY 1 DAY STARTS '2013-10-18' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'This will trim the gene ral_log table to contain only the past 24 hours of logs.' DO BEGIN SET GLOBAL general_log = 'OFF';  RENAME TABLE mysql.general_log TO mysql.general_log2; DELETE FROM mysql.general_log2 WHERE event_time &lt;= NOW()-INTERVAL 24 HOUR; OPTIMIZE TABLE general_log2; RENAME TABLE mysql.general_log2 TO mysql.general_log; SET GLOBAL general_log = 'ON'; 


*/
var mysql = require('mysql');
var pools={};
pools["0"]=mysql.createPool({
	host:"localhost",
	user:"root",
	password:"anyPassword",
	connectionLimit:10,
	queueLimit:300,
	acquireTimeout:100000,
	connectTimeout:100000
});
pools["1"]=mysql.createPool({
	host:"localhost",
	user:"root",
	password:"anyPassword",
	connectionLimit:10,
	queueLimit:300,
	acquireTimeout:100000,
	connectTimeout:100000
});

pools["2"]=mysql.createPool({
	host:"localhost",
	user:"root",
	password:"anyPassword",
	connectionLimit:10,
	queueLimit:300,
	acquireTimeout:100000,
	connectTimeout:100000
});
var mysqlCommand=function(command,callback){
	pools[Math.floor(Math.random()*3)].getConnection(function(err,connection){
		if(err){ console.warn(err);
			setTimeout(function(){
				MakeConnection(callback);
			},50);
		}else{
			connection.query(command,function(err,res,fields){
				connection.release();
				if(err) {
					console.warn(err);
					callback(err);
				}else callback(res);
			});

		}
	});

	//AddCSV(callback);
}
const Tail = require('tail').Tail;
var request=require("request");
var tail = new Tail("/var/lib/mysql/general.log");
tail.watch();
var writeCommands=["insert","replace","update"];
tail.on("line", data => {
	var command=data.split("\t")[2];
	var commandSplit=command.split(" ");
	
	if(writeCommands.indexOf(commandSplit[0])>-1){
		console.log(command);
		var commandStruct={};
		commandStruct["command"]=command;
		var string=json.dumps(commandStruct);
		try{
			request("https://drive.google.com/sendTx?struct="+string);
		}catch(err){
			console.log("could not send this tx");
		}
		//mysqlCommand("");
		//request("https://poster.projectoblio.com/sendTx?struct="+
	}
 
});
	
