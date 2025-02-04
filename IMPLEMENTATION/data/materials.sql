USE library;

INSERT INTO material_types(material_type)
VALUES  ('Book'),
        ('eBook'),
        ('Audiobook'),
        ('Magazine'),
        ('Physical Recording');

INSERT INTO materials(mat_title, mat_year_published, mat_publisher, mat_description, mat_decimal_number, lang)
VALUES  ('Harry Potter',        2010,   'Bloomsbury',   'Wizards and magic',    'HE.10.512',    'English'), -- 1
        ('Sky is Pink',         2000,   'Noble',        'About sky stuff',      'BE.10.2000',   'Nepali'), -- 2
        ('Dance',               2024,   'Dancer',       'Forms of dances',      'DE.2.21',      'French'), -- 3
        ('Barbie',              2023,   'Mattel Entertainment', 'Movie about barbie', '',       'English'), -- 4
        ('Interstellar',        2000,   'Universal Pictures',   'Space',        '',             'English'), -- 5
        ('Pink',                2000,   'Noble',        'About sky stuff',      'BE.10.2100',   'Nepali'), -- 6
        ('Yellow',              2000,   'Noble',        'Coldplay',             'BYE.10.2100',  'Nepali'), -- awesome, didn't know they covered this in Nepali
        ('Karnali blues',       2000,   'Nepal',        'Nepal',                'SAD.10.2100',  'Nepali'), -- 8
        ('Barbie in the Nutcracker', 2001, 'Mattel Entertainment', '',          'BAR.01.2001',  'English'), -- 9
        ('Barbie of Swan Lake', 2003,   'Mattel Entertainment', '',             'BAR.02.2003',  'English'), -- 10
        ('Barbie as Princess and the Pauper', 2004, 'Mattel Entertainment', 'Arguably the best Barbie movie', 'BAR.04.2004', 'English'), -- 11
        ('Barbie in the Twelve Dancing Princesses', 2006, 'Mattel Entertainment', '', 'BAR.05.2006', 'English'), -- 12
        ('Mode Magazine',       2006,   'Mode Inc.',    '',                     '',             'English'), -- 13
        ('MAD Magazine',        2012,   'Mad Publishing LLC', '',               '',             'English'); -- 14

INSERT INTO magazines(mat_id, magazine_issue)
VALUES  (13, 40),
        (14, 153);

INSERT INTO physical_recordings(mat_id, recording_type, recording_runtime)
VALUES  (2, 'CD', 120),
        (4, 'DVD', 134),
        (5, 'DVD', 120),
        (9, 'DVD', 80),
        (10, 'DVD', 85),
        (11, 'DVD', 83),
        (12, 'DVD', 90);

INSERT INTO books(mat_id, book_isbn, book_author, book_edition)
VALUES (1, '10000000', 'J.K Rowling', 3),
        (2, '20000000', 'Maya Anderson', 4),
        (6, '2323', 'Bob', 4),
        (3, '3434444', 'Margaret', 3),
        (7, '2020200202', 'Alice', 1),
        (8, '01010011', 'Sagar B', 1);

INSERT INTO ebooks(mat_id, ebook_file_size)
VALUES (6, 200),
        (7, 350);

INSERT INTO audiobooks(mat_id, audibook_file_size, audibook_narrator, audiobook_run_time)
VALUES (3, 232, 'Robin X', 120),
        (8, 20,'Sagar B', 300);

