CREATE DATABASE IF NOT EXISTS COP4331;

USE COP4331;

/* Creates the table of Users
Users have a:
	ID that does not need to be specified, as it is default and auto-increments
	firstname that is limited to 50 characters
	lastname that is limited to 50 characters
	username that is limited to 50 characters
	password  that is limited to 50 characters
	and a date that the account was created, which is defaulted to the time of creation
*/
	
CREATE TABLE IF NOT EXISTS User (
	ID int NOT NULL AUTO_INCREMENT,
	firstName VARCHAR(50) NOT NULL DEFAULT '',
	lastName VARCHAR(50) NOT NULL DEFAULT '',
	username VARCHAR(50) NOT NULL DEFAULT '',
	password VARCHAR(50) NOT NULL DEFAULT '',
	dateCreated DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (ID),
) ENGINE = InnoDB;

/* Creates the table of Contacts
Contacts have a:
	ID that does not need to be specified, as it is default and auto-increments
	firstname that is limited to 50 characters
	lastname that is limited to 50 characters
	email that is limited to 100 characters (mostly because of the way I formatted the test data
	phone  that is limited to 20 characters
	and the user ID of who it was created by
*/
CREATE TABLE IF NOT EXISTS Contacts (
	ID int NOT NULL AUTO_INCREMENT,
	firstName VARCHAR(50) NOT NULL DEFAULT '',
	lastName VARCHAR(50) NOT NULL DEFAULT '',
	email VARCHAR(100) NOT NULL DEFAULT '',
	phone VARCHAR(20) NOT NULL DEFAULT '',
	userID INT,
	PRIMARY KEY (ID),
	FOREIGN KEY (UserID) REFERENCES Users(ID)
) ENGINE = InnoDB;

--START OF TEST DATA INSERTION
INSERT INTO Users (FirstName, LastName, Username, Password) VALUES ('John', 'Smith', 'Jsmith', 'TheGoat123');
INSERT INTO Users (FirstName, LastName, Username, Password) VALUES ('Audrey', 'Bernstein', 'Abern', 'LeonardBernsteinWho?');
INSERT INTO Users (FirstName, LastName, Username, Password) VALUES ('John', 'Doe', 'Jdoe', 'NoRecord1994');
INSERT INTO Users (FirstName, LastName, Username, Password) VALUES ('Jane', 'Doe', 'Jdoe2', 'DiaryOfJane2016');
INSERT INTO Users (FirstName, LastName, Username, Password) VALUES ('Max', 'Farmer', 'Mfarmer', 'HonestWork2015');

INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('feikhen', 'bokhi', 'feikhen.bokhi@coldmail.com', '4094882455', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zaram', 'radar', 'zaram.radar@coldmail.com', '1152761423', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('eu', 'Forestweaver', 'eu.Forestweaver@hmail.com', '1685761892', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('durvid', 'Roughgust', 'durvid.Roughgust@yipee.com', '5431233411', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('pabel', 'ritsk', 'pabel.ritsk@coldmail.com', '7427793198', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('nom', 'glevrodz', 'nom.glevrodz@hmail.com', '0286024865', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('tragrum', 'Tuskguard', 'tragrum.Tuskguard@bpm.com', '0061389344', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('reth', 'Lonespear', 'reth.Lonespear@hmail.com', '6618496378', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('theheomus', 'dankhuelib', 'theheomus.dankhuelib@bpm.com', '9135984076', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('tava', 'zuehked', 'tava.zuehked@coldmail.com', '1542097372', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('drodjeslos', 'chekodzene', 'drodjeslos.chekodzene@bpm.com', '1657541200', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('booslet', 'vrolbune', 'booslet.vrolbune@coldmail.com', '6071777733', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('the', 'hi', 'the.hi@coldmail.com', '9818266038', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('ium', 'suen', 'ium.suen@hmail.com', '2509478351', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('fuerceru', 'gandino', 'fuerceru.gandino@bpm.com', '1640618984', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zurtue', 'vicinor', 'zurtue.vicinor@coldmail.com', '3988087783', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('shusatih', 'hama', 'shusatih.hama@hmail.com', '7107349651', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('shehreih', 'runol', 'shehreih.runol@hmail.com', '9683484992', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('shisvelol', 'Longash', 'shisvelol.Longash@yipee.com', '3337438088', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('riezna', 'Steelbash', 'riezna.Steelbash@hmail.com', '8198972282', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('linne', 'vesk', 'linne.vesk@bpm.com', '0781586124', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('hen', 'stodrisk', 'hen.stodrisk@hmail.com', '8626539246', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zelilo', 'Wolfhair', 'zelilo.Wolfhair@coldmail.com', '2119762952', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('weray', 'Grassbeard', 'weray.Grassbeard@hmail.com', '3918195325', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('vesithi', 'rilravafk', 'vesithi.rilravafk@bpm.com', '8677229419', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('chazoh', 'riltuehd', 'chazoh.riltuehd@hmail.com', '8255491250', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('nulverel', 'tamevrere', 'nulverel.tamevrere@bpm.com', '9396799769', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zurresh', 'melbabe', 'zurresh.melbabe@coldmail.com', '7665825441', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('yao', 'nu', 'yao.nu@hmail.com', '6335532825', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('pui', 'siam', 'pui.siam@coldmail.com', '1862146291', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('fadd', 'bisceru', 'fadd.bisceru@yipee.com', '3649293440', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('mamj', 'zuscisqa', 'mamj.zuscisqa@coldmail.com', '6342887581', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('numar', 'hassur', 'numar.hassur@bpm.com', '7444442930', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('hahmud', 'dihru', 'hahmud.dihru@yipee.com', '8730382520', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('miu', 'Greenbough', 'miu.Greenbough@coldmail.com', '2975343211', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('tugrirn', 'Fargust', 'tugrirn.Fargust@yipee.com', '5351222640', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('glarif', 'disk', 'glarif.disk@yipee.com', '3400531067', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('gar', 'nokon', 'gar.nokon@coldmail.com', '0454956482', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('medven', 'Fourtrapper', 'medven.Fourtrapper@yipee.com', '8314849207', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('frurth', 'Ambergrain', 'frurth.Ambergrain@hmail.com', '0705673849', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('nukid-kod', 'johpuucrufk', 'nukid-kod.johpuucrufk@bpm.com', '5774579830', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zojud', 'nuhpifk', 'zojud.nuhpifk@bpm.com', '3671554026', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('sadeslauc', 'metvedungu', 'sadeslauc.metvedungu@bpm.com', '1117300483', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('shoosdac', 'mukumzu', 'shoosdac.mukumzu@hmail.com', '2903885770', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zo', 'qao', 'zo.qao@bpm.com', '3074783710', '5');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('hew', 'ya', 'hew.ya@hmail.com', '3450145620', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('tuerfimbiz', 'eldovi', 'tuerfimbiz.eldovi@yipee.com', '6667677191', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('fronchoz', 'jasobro', 'fronchoz.jasobro@hmail.com', '7276513995', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('silodoh', 'cekhid', 'silodoh.cekhid@yipee.com', '6532444279', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zisnah', 'rikhi', 'zisnah.rikhi@hmail.com', '7315785832', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('sosrielil', 'Emberkeeper', 'sosrielil.Emberkeeper@bpm.com', '1595106453', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('rierlull', 'Hillsurge', 'rierlull.Hillsurge@bpm.com', '9134746365', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('halrel', 'vov', 'halrel.vov@bpm.com', '1031552217', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('se', 'nomov', 'se.nomov@hmail.com', '2363035280', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('hilari', 'Humbletrap', 'hilari.Humbletrap@coldmail.com', '2591085079', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('vivra', 'Gloweye', 'vivra.Gloweye@bpm.com', '3410485925', '1');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zishothu', 'vohkahrald', 'zishothu.vohkahrald@hmail.com', '3958279617', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('foha', 'meltrifk', 'foha.meltrifk@yipee.com', '9034175332', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('murversa', 'trovzumetva', 'murversa.trovzumetva@hmail.com', '2908745680', '2');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('zufi', 'godurnye', 'zufi.godurnye@yipee.com', '4313630190', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('niao', 'lum', 'niao.lum@hmail.com', '9314820559', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('tai', 'duem', 'tai.duem@yipee.com', '8748143552', '3');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('omd', 'dusquze', 'omd.dusquze@bpm.com', '9295945058', '4');
INSERT INTO Contacts (FirstName, LastName, Email, Phone, UserID) VALUES ('bencf', 'mevomor', 'bencf.mevomor@bpm.com', '1322270313', '1');