/* -- DATABASE INITIALIZATION FILE
	This file creates the lib_db database and calls all necessary files to create [and fill] all tables.
	Tables are currently organized by the order in which they need to be called.
	Refer to the ER diagram for table/field names and relationships.
*/

DROP DATABASE IF EXISTS library;
CREATE DATABASE library;
USE library;

/* -- BUILD TABLES ----------------------------------------------------------- */

-- (J) validation_tables.sql (languages, equipment_types, recording_types, loan_types)
	DROP TABLE IF EXISTS languages;
	CREATE TABLE languages (
		PRIMARY KEY (lang), -- 'language' is considered a keyword in MariaDB, so it is shortened here to lang
		lang VARCHAR(64) 
	);
	DROP TABLE IF EXISTS equipment_types;
	CREATE TABLE equipment_types (
		PRIMARY KEY (equipment_type),
		equipment_type     VARCHAR(64),
		equipment_can_loan BOOLEAN
	);
	DROP TABLE IF EXISTS recording_types;
	CREATE TABLE recording_types (
		PRIMARY KEY (recording_type),
		recording_type VARCHAR(64)
	);
	DROP TABLE IF EXISTS material_types;
	CREATE TABLE material_types (
		PRIMARY KEY (material_type),
		material_type VARCHAR(200)
	);
	DROP TABLE IF EXISTS loan_types;
	CREATE TABLE loan_types (
		PRIMARY KEY (loan_type),
		loan_type 		   VARCHAR(200),
		loan_type_duration INT(3)
	);

-- (A) users.sql (users, clubs, club_members) 
	DROP TABLE IF EXISTS users;
	CREATE TABLE users (
		PRIMARY KEY (user_id),
		user_id				CHAR(6), -- would like some way to make this 6 digits
		user_first_name		VARCHAR(64),
		user_last_name		VARCHAR(64),
		user_address		VARCHAR(128),
		user_phone			VARCHAR(14),	-- (XXX) XXX-XXX
		user_email			VARCHAR(128)
	);
	DROP TABLE IF EXISTS staff;
	CREATE TABLE staff (
		PRIMARY KEY (user_id),
		user_id		CHAR(6),
		FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT
	);
	DROP TABLE IF EXISTS clubs;
	CREATE TABLE clubs (
		PRIMARY KEY (club_name),	-- we actually may need an artificial ID in case someone wants to change their club name :/
		club_name	VARCHAR(64),
		user_id		CHAR(6),
		FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
	);
	DROP TABLE IF EXISTS club_members;
	CREATE TABLE club_members (
		PRIMARY KEY (club_name, user_id),
		club_name	VARCHAR(64),
		user_id		CHAR(6),
		FOREIGN KEY (club_name) REFERENCES clubs(club_name) ON DELETE RESTRICT,
		FOREIGN KEY (user_id)	REFERENCES users(user_id)	ON DELETE CASCADE
	);

-- (C) spaces.sql (spaces, space_reservations)
	DROP TABLE IF EXISTS spaces;
	CREATE TABLE spaces (
		PRIMARY KEY (space_id),
		space_id                INT NOT NULL,
		space_name              VARCHAR(64) NOT NULL,  
		space_capacity          INT NOT NULL
	);
	DROP TABLE IF EXISTS space_reservations;
	CREATE TABLE space_reservations (
		res_id                    INT AUTO_INCREMENT,
		space_id                  INT NOT NULL, 
		user_id                   CHAR(6) NOT NULL,
		space_res_date            date,
		space_res_start_time      datetime,
		space_res_end_time        datetime,      
		PRIMARY KEY (res_id),
		FOREIGN KEY (space_id)	REFERENCES spaces(space_id) ON DELETE CASCADE,
		FOREIGN KEY (user_id)	REFERENCES users(user_id) ON DELETE CASCADE
	);

-- (J) equipment.sql
	DROP TABLE IF EXISTS equipment;
	CREATE TABLE equipment (
		PRIMARY KEY (equipment_id),
		equipment_id   INT AUTO_INCREMENT,
		equipment_type VARCHAR(64),
		space_id       INT,
		equipment_qty  INT,
		FOREIGN KEY (equipment_type) REFERENCES equipment_types(equipment_type),
		FOREIGN KEY (space_id)       REFERENCES spaces(space_id) ON DELETE SET NULL
	);

-- (S) materials.sql (materials, magazines, physical_recordings, books, ebooks, audiobooks) 
	DROP TABLE IF EXISTS materials;
	CREATE TABLE materials (
		PRIMARY KEY         (mat_id),
		mat_id              INT AUTO_INCREMENT,
		mat_title           VARCHAR(64),
		mat_year_published  INT,
		mat_publisher       VARCHAR(200),
		mat_description     VARCHAR(1000),
		mat_decimal_number  VARCHAR(200),
		lang                VARCHAR(64),
		FOREIGN KEY (lang) REFERENCES languages(lang) ON DELETE RESTRICT
	);
	DROP TABLE IF EXISTS magazines;
	CREATE TABLE magazines (
		PRIMARY KEY (mat_id),
		mat_id INT,
		magazine_issue INT, -- issue is serial#
		FOREIGN KEY (mat_id) REFERENCES materials(mat_id) ON DELETE CASCADE
	);
	DROP TABLE IF EXISTS physical_recordings;
	CREATE TABLE physical_recordings (
		PRIMARY KEY (mat_id),
		mat_id INT,
		recording_type VARCHAR(64),
		recording_runtime INT,
		FOREIGN KEY (mat_id) REFERENCES materials(mat_id) ON DELETE CASCADE,
		FOREIGN KEY (recording_type) REFERENCES recording_types(recording_type) ON DELETE RESTRICT
	);
	DROP TABLE IF EXISTS books;
	CREATE TABLE books (
		PRIMARY KEY (mat_id),
		mat_id INT,
		book_isbn VARCHAR(13),
		book_author VARCHAR(60),
		book_edition INT,
		FOREIGN KEY (mat_id) REFERENCES materials(mat_id) ON DELETE CASCADE
	);
	DROP TABLE IF EXISTS ebooks;
	CREATE TABLE ebooks (
		PRIMARY KEY (mat_id),
		mat_id INT,
		ebook_file_size INT,
		FOREIGN KEY (mat_id) REFERENCES books(mat_id) ON DELETE CASCADE
	);
	DROP TABLE IF EXISTS audiobooks;
	CREATE TABLE audiobooks (
		PRIMARY KEY (mat_id),
		mat_id INT,
		audibook_file_size INT,
		audiobook_run_time INT,
		audibook_narrator VARCHAR (200),
		FOREIGN KEY (mat_id) REFERENCES books(mat_id) ON DELETE CASCADE
	);

-- (A) franchises.sql (franchises, franchise_entries)
    DROP TABLE IF EXISTS franchises;
    CREATE TABLE franchises (
        PRIMARY KEY         (franchise_id),
        franchise_id        INT AUTO_INCREMENT,
        franchise_name		VARCHAR(128)
    );
    DROP TABLE IF EXISTS franchise_entries;
    CREATE TABLE franchise_entries (
        PRIMARY KEY     (franchise_id, mat_id),
        franchise_id    INT,
        mat_id          INT,
        FOREIGN KEY (franchise_id)  REFERENCES franchises(franchise_id)	ON DELETE RESTRICT,
        FOREIGN KEY (mat_id)        REFERENCES materials(mat_id)		ON DELETE RESTRICT
    );

-- (J) loans.sql (loans, material_loans, equipment_loans)
	DROP TABLE IF EXISTS loans;
	CREATE TABLE loans (
		PRIMARY KEY (loan_id),
		loan_id              INT AUTO_INCREMENT,
		user_id              CHAR(6),
		loan_checkout_date   DATE,
		loan_expected_return DATE,
		loan_actual_return   DATE,
		loan_type 			 VARCHAR(200),
		FOREIGN KEY (user_id)   REFERENCES users(user_id) 		 ON DELETE CASCADE,
		FOREIGN KEY (loan_type) REFERENCES loan_types(loan_type) ON DELETE RESTRICT
	);
	DROP TABLE IF EXISTS material_loans;
	CREATE TABLE material_loans (
		PRIMARY KEY (loan_id),
		loan_id INT,
		mat_id  INT,

		FOREIGN KEY (loan_id) REFERENCES loans(loan_id)    ON DELETE CASCADE,
		FOREIGN KEY (mat_id)  REFERENCES materials(mat_id) ON DELETE CASCADE
	);
	DROP TABLE IF EXISTS equipment_loans;
	CREATE TABLE equipment_loans (
		PRIMARY KEY (loan_id),
		loan_id      INT,
		equipment_id INT,

		FOREIGN KEY (loan_id)      REFERENCES loans(loan_id)          ON DELETE CASCADE,
		FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE
	);

-- (C) reservations.sql (material_reservations, equipment_reservations)
	DROP TABLE IF EXISTS material_reservations;
	CREATE TABLE material_reservations (
		mat_res_id                INT AUTO_INCREMENT,
		mat_id                    INT NOT NULL,
		user_id                   CHAR(6) NOT NULL,
		mat_res_start_time        DATETIME,
		mat_res_end_time          DATETIME,
		PRIMARY KEY (mat_res_id),
		FOREIGN KEY (mat_id) REFERENCES materials(mat_id) ON DELETE CASCADE,
		FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE 
	);
	DROP TABLE IF EXISTS equipment_reservations;
	CREATE TABLE equipment_reservations (
		equipment_res_id          INT AUTO_INCREMENT,
		equipment_id              INT NOT NULL,
		user_id                   CHAR(6) NOT NULL,
		equipment_res_start_time        datetime,
		equipment_res_end_time          datetime,      
		PRIMARY KEY (equipment_res_id),
		FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE,
		FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
	);

-- (S) view: late_fees (NOT SURE IF WE NEED THIS RN?)

	DELIMITER //

/* -- FUNCTIONS --------------------------------------------------------------- */
	DROP FUNCTION IF EXISTS loan_is_active;
	CREATE FUNCTION  loan_is_active(l_id INT) -- loan_id
	RETURNS BOOLEAN
	RETURN (SELECT loan_actual_return IS NULL
			  FROM loans
			 WHERE loan_id = l_id);
	//

	DROP FUNCTION IF EXISTS most_recent_mat_loan;
	CREATE FUNCTION most_recent_mat_loan(m_id INT)
	RETURNS INT
	BEGIN
		SET @mat = m_id;
		SET @most_recent = (SELECT MAX(loan_checkout_date)
							  FROM loans
							  	   INNER JOIN material_loans
								   USING (loan_id)
							 WHERE mat_id = @mat);

		RETURN (SELECT loan_id
				FROM loans
					INNER JOIN material_loans
					USING (loan_id)
				WHERE loan_checkout_date = @most_recent
					AND mat_id = @mat);
	END
	//

	DROP FUNCTION IF EXISTS most_recent_equip_loan;
	CREATE FUNCTION most_recent_equip_loan(e_id INT)
	RETURNS INT
	BEGIN
		SET @equip = e_id;
		SET @most_recent = (SELECT MAX(loan_checkout_date)
							  FROM loans
							  	   INNER JOIN equipment_loans
								   USING (loan_id)
							 WHERE equipment_id = @equip);

		RETURN (SELECT loan_id
				FROM loans
					INNER JOIN equipment_loans
					USING (loan_id)
				WHERE loan_checkout_date = @most_recent
					AND equipment_id = @equip);
	END
	//

	-- DROP FUNCTION IF EXISTS get_mat_return_date;
	-- DELIMITER $$
	-- CREATE FUNCTION get_return_date(targ_mat INT, checkout_date DATE)
	-- 	RETURNS DATE
	-- 	BEGIN
	-- 		DECLARE add_date INT;
	-- 		-- DECLARE $return_date DATE;
	-- 		IF (SELECT mat_id FROM books WHERE (mat_id = targ_mat) IS NOT NULL) THEN
	-- 			SET add_date = 4;
	-- 		ELSE IF ((SELECT mat_id FROM magazines WHERE (mat_id = targ_mat) IS NOT NULL) || (SELECT mat_id FROM magazines WHERE (mat_id = targ_mat) IS NOT NULL)) THEN
	-- 			SET add_date = 2;
	-- 		ELSE 
	-- 			SET add_date = 0;
	-- 		END IF;

	-- 		RETURN ( SELECT DATEADD(week, add_date, checkout_date) AS DateAdd );
	-- 	END;
	-- 	$$
	-- DELIMITER ;


/* -- TRIGGERS ---------------------------------------------------------------- */
	
	-- Enforcing linear chronology
	DROP TRIGGER IF EXISTS enforce_loan_actual_return_insert;
	CREATE TRIGGER enforce_loan_actual_return_insert
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET @checkout = NEW.loan_checkout_date;
		SET @actual_return = NEW.loan_actual_return;
		IF @actual_return < @checkout THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "loan_actual_return is before loan_checkout_date!";
		END IF;
	END;
	//

	DROP TRIGGER IF EXISTS enforce_loan_actual_return_update;
	CREATE TRIGGER enforce_loan_actual_return_update
	BEFORE UPDATE ON loans FOR EACH ROW
	BEGIN
		SET @checkout = NEW.loan_checkout_date;
		SET @actual_return = NEW.loan_actual_return;
		IF @actual_return < @checkout THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "loan_actual_return is before loan_checkout_date!";
		END IF;
	END;
	//

	DROP TRIGGER IF EXISTS enforce_loan_expected_return_insert;
	CREATE TRIGGER enforce_loan_expected_return_insert
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET	@checkout = NEW.loan_checkout_date;
		SET @expected_return = NEW.loan_expected_return;
		IF @expected_return < @checkout THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "loan_expected_return is before loan_checkout_date!";
		END IF;
	END;
	//

	DROP TRIGGER IF EXISTS enforce_loan_expected_return_update;
	CREATE TRIGGER enforce_loan_expected_return_update
	BEFORE UPDATE ON loans FOR EACH ROW
	BEGIN
		SET @checkout = NEW.loan_checkout_date;
		SET @expected_return = NEW.loan_expected_return;
		IF @expected_return < @checkout THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "loan_expected_return is before loan_checkout_date!";
		END IF;
	END;
	//

	-- Make sure that the material to be loaned is actually available for loaning
	DROP TRIGGER IF EXISTS ensure_available_material;
	CREATE TRIGGER ensure_available_material
	BEFORE INSERT ON material_loans FOR EACH ROW
	BEGIN
		SET @loan = NEW.loan_id;

		SET @material = NEW.mat_id;

		SET @error_message = CONCAT("Loan ", NEW.loan_id, " cannot be added. Material ", @material, " is still on loan.");

		IF loan_is_active(most_recent_mat_loan(@material)) THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//

	-- Make sure that the equipment to be loaned is actually available for loaning
	DROP TRIGGER IF EXISTS ensure_available_equipment;
	CREATE TRIGGER ensure_available_equipment
	BEFORE INSERT ON equipment_loans FOR EACH ROW
	BEGIN
		SET @loan = NEW.loan_id;

		SET @equipment = NEW.equipment_id;

		SET @error_message = CONCAT("Loan ", NEW.loan_id, " cannot be added. Equipment ", @equipment, " is still on loan.");

		IF loan_is_active(most_recent_equip_loan(@equipment)) THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//
	-- This automatically adds loan_expected_return to the loans table based on the type of thing loaned
	DROP TRIGGER IF EXISTS automatic_loan_expected_return;
	CREATE TRIGGER automatic_loan_expected_return
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET @id = NEW.loan_id;

		SET @duration = (SELECT loan_type_duration
						   FROM loan_types
						  WHERE loan_type = NEW.loan_type);

		SET @expected_return = (SELECT ADDDATE(NEW.loan_checkout_date, @duration));
		
		SET NEW.loan_expected_return = @expected_return;
	END;
	//

	-- Enforcing loan quantities
	DROP TRIGGER IF EXISTS enforce_material_loan_amt;
	CREATE TRIGGER enforce_material_loan_amt
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET @user = NEW.user_id;
		
		SET @num_active_loans = (SELECT COUNT(*) 
							       FROM loans
							      WHERE user_id = @user
							  	        AND loan_is_active(loan_id)
										AND loan_type = 'Book');

		SET @error_message = (CONCAT('User ', @user, ' already has 8 books on loan.'));

		IF @num_active_loans = 8 THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//

	DROP TRIGGER IF EXISTS enforce_equipment_loan_amt;
	CREATE TRIGGER enforce_equipment_loan_amt
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET @user = NEW.user_id;
		
		SET @num_active_loans = (SELECT COUNT(*) 
							       FROM loans
							      WHERE user_id = @user
							  	        AND loan_is_active(loan_id)
										AND loan_type = 'Equipment');

		SET @error_message = (CONCAT('User ', @user, ' already has 2 pieces of equipment on loan.'));
		
		IF @num_active_loans = 2 THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//

	DROP TRIGGER IF EXISTS enforce_multimedia_loan_amt;
	CREATE TRIGGER enforce_material_loan_amt
	BEFORE INSERT ON loans FOR EACH ROW
	BEGIN
		SET @user = NEW.user_id;
		
		SET @num_active_loans = (SELECT COUNT(*) 
							       FROM loans
							      WHERE user_id = @user
							  	        AND loan_is_active(loan_id)
										AND loan_type = 'Multimedia');

		SET @error_message = (CONCAT('User ', @user, ' already has 4 multimedia materials on loan.'));
		
		IF @num_active_loans = 4 THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//

	-- Enforce rule that there are at most two loan renewals.
	-- Uses length of time from checkout to expected return dates.
	DROP TRIGGER IF EXISTS enforce_max_loan_renewals;
	CREATE TRIGGER enforce_max_loan_renewals
	BEFORE UPDATE ON loans FOR EACH ROW
	BEGIN
		SET @duration = (SELECT 3 * loan_type_duration
		 				   FROM loan_types
						  WHERE loan_type = NEW.loan_type);

		SET @loan = OLD.loan_id;

		SET @max_return_date = (SELECT ADDDATE(NEW.loan_checkout_date, @duration));

		SET @error_message = (CONCAT("Loan ", @loan, " has already reached its max number of renewals."));

		IF NEW.loan_expected_return > @max_return_date THEN
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @error_message;
		END IF;
	END;
	//


	DELIMITER ;

	/* -- INSERT DATA INTO TABLES ------------------------------------------------- */
	SOURCE data/validation_tables.sql;
	SOURCE data/users.sql;
	SOURCE data/spaces.sql;
	SOURCE data/equipment.sql;
	SOURCE data/materials.sql;
	SOURCE data/franchises.sql;
	SOURCE data/loans.sql;
	SOURCE data/reservations.sql;