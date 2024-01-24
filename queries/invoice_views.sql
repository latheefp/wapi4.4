create view invoice_views  as SELECT
invoices.*, accounts.company_name
from invoices
LEFT JOIN accounts  on invoices.account_id=accounts.id