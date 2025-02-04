SELECT mat_id AS "id", mat_title AS "Title", mat_year_published AS "Year", mat_publisher AS "Publisher",
    mat_decimal_number AS "Decimal", lang AS "Language"
FROM materials
INNER JOIN magazines using (mat_id);