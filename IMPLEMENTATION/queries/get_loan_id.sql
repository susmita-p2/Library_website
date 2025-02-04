USE library;
SELECT loan_id FROM loans
WHERE user_id = (?)
ORDER BY loan_id DESC
LIMIT 1;