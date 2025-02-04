SELECT loan_id FROM
    (SELECT loan_id FROM loans WHERE loan_actual_return IS NULL)
    WHERE loan_id = (?);