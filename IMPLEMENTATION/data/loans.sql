/* This file creates the loans table and its subset tables:  material_loans and equipment_loans. */

USE library;

/* Populate with sample data */
INSERT INTO loans (user_id, loan_type, loan_checkout_date, loan_expected_return, loan_actual_return)
VALUES ('000001', 'Equipment', '2005-03-02', '2005-03-05', '2005-03-04'),   -- equipment
       ('000001', 'Equipment', '2005-03-02', '2005-03-05', '2005-03-04'),   -- equipment
       ('000001', 'Book',       '2005-03-02', '2005-03-30', '2005-03-24'),   -- book
       ('000001', 'Book',       '2005-03-02', '2005-03-30', '2005-03-28'),   -- book
       ('000002', 'Book',       '2024-10-02', '2024-10-30', '2024-10-14'),   -- book
       ('000002', 'Book',       '2024-10-02', '2024-10-30', '2024-10-14'),   -- book
       ('000002', 'Book',       '2024-10-02', '2024-10-30', '2024-10-14'),   -- book
       ('000003', 'Book',       '2024-10-31', '2024-11-02', '2024-11-02'),   -- book
       ('000003', 'Book',       '2024-10-05', '2024-11-02', '2024-11-14'),   -- book
       ('000004', 'Multimedia', '2024-10-20', '2024-11-03', '2024-10-20'),   -- multimedia
       ('000004', 'Multimedia', '2024-10-20', '2024-11-03', '2024-10-22'),   -- multimedia
       ('000004', 'Equipment',  '2024-10-20', NULL, NULL),           -- equipment
       ('000001', 'Book',       '2024-11-11', NULL, NULL);           -- book

INSERT INTO equipment_loans (loan_id, equipment_id)
VALUES ('000001',    5),
       ('000002',    6),
       ('000012',    7);

INSERT INTO material_loans (loan_id, mat_id)
VALUES (3,  1),
       (4,  2),
       (5,  3),
       (6,  8),
       (7,  6),
       (8,  7),
       (9,  8),
       (10, 5), -- multimedia
       (11, 2), -- multimedia
       (13, 1);