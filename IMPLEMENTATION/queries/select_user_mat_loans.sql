SELECT loan_id AS "Loan #", user_id AS "User ID", mat_id AS "Material ID", loan_checkout_date AS "Checkout date",
  loan_expected_return AS "Expected return date", loan_actual_return AS "Return date" 
    FROM material_loans  INNER JOIN loans USING (loan_id)
    WHERE user_id = (?) 
    ORDER BY loan_id DESC;