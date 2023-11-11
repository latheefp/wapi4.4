create view recent_chats as SELECT
    MAX(streams.id) as id,
    MAX(streams.created) as created,
    streams.contact_stream_id,
    contact_streams.contact_number,
    contact_streams.name,
    contact_streams.profile_name
FROM
    streams
LEFT JOIN contact_streams ON contact_streams.id = streams.contact_stream_id
GROUP BY
    contact_stream_id
ORDER BY
    id
DESC