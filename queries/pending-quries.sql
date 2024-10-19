#create blocked numbers
blocked_numbers
update contacts_streams
bin/cake bake model contact_streams
update contact_streams set contact_streams.account_id=1; 


INSERT INTO `users`(
    `id`,
    `last_logged`,
    `show_closed`,
    `ugroup_id`,
    `account_id`,
    `name`,
    `username`,
    `password`,
    `email`,
    `mobile_number`,
    `active`,
    `created`,
    `modified`,
    `show_cols`,
    `login_count`
)
VALUES(
    '1',
    '2024-10-18 11:38:42.000000',
    '0',
    '',
    '0',
    'system User',
    'system',
    '',
    'admin@egrand.in',
    NULL,
    NULL,
    '2024-10-18 11:38:42.000000',
    '2024-10-18 11:38:42.000000',
    NULL,
    NULL
),(
    NULL,
    '2024-10-18 11:38:42.000000',
    '1',
    '',
    '0',
    '',
    '',
    '',
    '',
    NULL,
    '0',
    '2024-10-18 11:38:42.000000',
    '2024-10-18 11:38:42.000000',
    NULL,
    NULL
)


UPDATE contact_streams set user_id=1 where user_id=0; 


#should be zero.
select contact_streams.contact_number from contact_streams where contact_streams.id not in(SELECT UNIQUE streams.contact_stream_id from streams);  

#updateed stream_views.
 data_puls_bool_ref_field  add in flagship.



#current image: latheefp/wapi:20241013_6dede8b