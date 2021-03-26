# -*- coding: utf-8 -*-
# General Idea taken from  lygie work
# CSV format head= mail;attr_id1;attr_id2;attr_id3;attr_id4;attr_idn
# CSV format body= "mail@mydomain";"attr_val1";attr_val2;att_val3;"attr_val4";attrvaln
#run it = php bulk_insert.php
#example of CVS:
#correo;22;10;11;30;31;32;9
#"xxxxdddfff@madmail.com";782;"name";"surname";"N";"S";"";"M"
#"asd3423asd@soft.net";783;"name";"surname";"S";"S";"";"M"
#"asdasd32@hardmail.net";784;"name";"surname";"S";"N";"";"F"
#"dadsa32323@gmail.com";785;"AAA";"XXXXXXXXXXX";"S";"S";"";"F"

import MySQLdb,csv

#location of csvfile
CSV="usuarios.csv"
#$database_host = "localhost";
database_host = "10.3.0.130"
#what is the name of the database we are using
database_name = "phplist"
# who do we log in as?
database_user = "root"
# and what password do we use
database_password = 'root'
#me conecto a la bbdd
conexion = MySQLdb.connect(host=database_host, user=database_user, passwd=database_password, db=database_name)

if (conexion):
#	print "todo bien"
	pass
else:
	print "opps"

#abro el archivo csv
Daten = csv.reader(open(CSV, 'rb'), delimiter=';', quotechar='"')

Zeilen=0

for row in Daten:
	Zeilen=Zeilen+1
	#cantcampos = count(Daten)
	#salvamos un array con los id de atributo - need to keep the attrib ids somewhere 
	if Zeilen==1:
		acampos=row
	else:
		valor=row[0]
		valor=valor.replace("'","")
		sql="SELECT id FROM phplist.phplist_user_user where email='"+valor+"';"
		cursor = conexion.cursor()
		cursor.execute(sql)
		record=cursor.fetchone()
		if record:		#verificamos que usuario existe! - check if user exists! to avoid too much inserts each time
			Id=record[0]
		else:	
			sql="INSERT INTO phplist_user_user (email,confirmed,htmlemail) VALUES ('"+valor+"',1,1);"
			cursor = conexion.cursor()
			cursor.execute(sql)
			Id=cursor.insert_id()
		for i in range (1,len(row)):	#pass trough csv cols with attr_id
			sql="DELETE FROM phplist_user_user_attribute WHERE attributeid="+acampos[i]+" AND userid="+str(Id)
			cursor = conexion.cursor()
			cursor.execute(sql)
						#insert new attribute values for each user
			valor=row[i].replace("'","")
			sql="INSERT INTO phplist_user_user_attribute (attributeid, userid, value) VALUES ("+acampos[i]+","+str(Id)+",'"+valor+"');"
			cursor = conexion.cursor()			
			cursor.execute(sql)			


















