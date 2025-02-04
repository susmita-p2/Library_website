/* This file creates the validation tables: languages, equipment_types, and recording_types. */
USE library;

/* Populate tables with sample data */
INSERT INTO languages (lang)
VALUES ('English'),
       ('Spanish'),
       ('French'),
       ('German'),
       ('Latin'),
       ('Arabic'),
       ('Hindi'),
       ('Urdu'),
       ('Indonesian'),
       ('Mandarin'),
       ('Japanese'),
       ('Nepali');

INSERT INTO equipment_types (equipment_type, equipment_can_loan)  
VALUES ('Desktop',     False),
       ('Laptop',       True),
       ('Telephone',   False),
       ('FAX',         False),
       ('Printer',     False),
       ('Projector',   True),
       ('Headphones',  True),
       ('CD Player',   True),
       ('DVD Player',  True),
       ('Tape Player', True);

INSERT INTO recording_types (recording_type)
VALUES ('DVD'),
       ('Blu-Ray'),
       ('CD'),
       ('VHS'),
       ('VHS-C'),
       ('Cassette'),
       ('Vinyl Record'),
       ('DAT');

INSERT INTO loan_types (loan_type, loan_type_duration)
VALUES ('Book',       28),
       ('Equipment',  3),
       ('Multimedia', 14);