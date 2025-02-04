/* This file creates the users, clubs, and club_members tables. */

-- How do we account for routine data management without using functions? Are we supposed to use functions ??
USE library;

INSERT INTO users(user_id, user_first_name, user_last_name, user_address, user_phone, user_email)
VALUES	('000001', 'Walter', 'White', '308 Negra Arroyo Ln, Albequerque NM 87111', '(505) 555-5505', 'wwhite@email.com'),
		('000002', 'Noah', 'Dyck',	'100 Shmelly Ave, Letterkenny ON P3G',		'',					''),
		('000003', 'Bria', 'Le',		'386 Silver Spear Ct, Coraopolis PA 15108', '(402) 434-5634', 'ble@email.com'),
		('000004', 'Hugh', 'Mann',	'123 Street Ln, Townsville Ohio 43058',		'(613) 478-2498', 'hmann@email.com'),
		('000005', 'Dude', 'Guy',		'462 Address St, Chicago IL 11111',			'(111) 111-1111', 'dguy@email.com');

INSERT INTO staff(user_id)
VALUES  ('000005');

INSERT INTO clubs(club_name, user_id)
VALUES	('Pickling Club',	'000002'),
		('Chemistry Club',	'000001'),
		('Film Club',		'000003');

INSERT INTO club_members(club_name, user_id)
VALUES	('Pickling Club',	'000004'),
		('Film Club',		'000004'),
		('Chemistry Club',	'000003');
