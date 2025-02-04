/* This file creates the equipment table. */

USE library;

/* Populate table with sample data */
INSERT INTO equipment (equipment_type, space_id, equipment_qty)
VALUES ('Desktop',    1, 0), -- we can use 0 to show it can't be checked out
       ('Desktop',    1, 0),
       ('Printer',    1, 0),
       ('FAX',        1, 0),
       ('Headphones', 2, 10),
       ('Projector',  2, 3),
       ('Laptop',     2, 5),
       ('Desktop',    3, 0),
       ('Desktop',    3, 0),
       ('Desktop',    3, 0),
       ('Desktop',    3, 0),
       ('Printer',    3, 0);