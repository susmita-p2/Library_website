
SELECT loan_id AS "Loan#", user_id AS "User#", mat_id AS "Material ID#", loan_checkout_date AS "Checkout",
 loan_expected_return AS "Expected Return", loan_actual_return AS "Actual Return" 
  FROM material_loans INNER JOIN loans USING (loan_id) ORDER BY loan_id DESC LIMIT 5;
