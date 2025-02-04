/* This file creates the franchises table. */
USE library;

/* POPULATE TABLES -------------------------- */
    INSERT INTO franchises(franchise_name)
    VALUES      ('Barbie Cinematic Universe'),
                ('Warriors Series'),        -- Erin Hunter
                ('Percy Jackson'),           -- Rick Riordon
                ('His Dark Materials');     -- Phillip Pullman

    INSERT INTO franchise_entries(franchise_id, mat_id)
    VALUES  (1, 4),
            (1, 9),
            (1, 10),
            (1, 11),
            (1, 12);