DROP view contact_numbers_views;
create view contact_numbers_views  as 
SELECT
    contact_numbers.*,
    contacts.name contact_name,
    contacts.user_id,
    contacts.created,
    contacts_contact_numbers.contact_id,
    users.account_id
FROM
    contact_numbers
LEFT JOIN contacts_contact_numbers ON contact_numbers.id = contacts_contact_numbers.contact_number_id
LEFT JOIN contacts ON contacts.id = contacts_contact_numbers.contact_id
LEFT JOIN users ON contacts.user_id = users.id;