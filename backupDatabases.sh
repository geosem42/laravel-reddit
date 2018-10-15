#!/bin/sh
# Used to backup databases so that we can keep a common username/passwords for example users
# Can be used to create sample users with different arrows and karmas easier
chmod 400 ./sshKey;
ssh -i "./sshKey" root@poster.projectoblio.com "mysqldump -uroot -panyPassword irt > ~/databaseBackup1.sql";
scp -i "./sshKey" root@poster.projectoblio.com:~/databaseBackup1.sql ./backupDatabases/
