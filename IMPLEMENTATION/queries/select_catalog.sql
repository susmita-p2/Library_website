SELECT mat_id AS "id", mat_title AS "Title", mat_year_published AS "Year", mat_publisher AS "Publisher",
    mat_decimal_number AS "Decimal", lang AS "Language"
FROM materials
-- eventually, create some function to detect membership of a subset table & call it here
-- to have a column that shows what type the material is.