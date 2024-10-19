SELECT
    Streams.lang AS Streams__lang,
    Streams.type AS Streams__type,
    Streams.message_from AS Streams__message_from,
    Streams.id AS Streams__id,
    Streams.account_id AS Streams__account_id,
    Schedules.name AS Schedules__name,
    Campaigns.id AS Campaigns__id,
    Campaigns.campaign_name AS Campaigns__campaign_name,
    ContactStreams.contact_number AS ContactStreams__contact_number,
    ContactStreams.profile_name AS ContactStreams__profile_name,
    ContactStreams.name AS ContactStreams__name
FROM
    streams Streams
LEFT JOIN campaigns Campaigns ON
    Campaigns.id = Schedules.campaign_id
LEFT JOIN schedules Schedules ON
    Schedules.id = Streams.schedule_id
LEFT JOIN contact_streams ContactStreams ON
    ContactStreams.id = Streams.contact_stream_id
WHERE
    (
        Streams.account_id = 1 AND(
            ContactStreams.contact_number LIKE "latheef" OR ContactStreams.profile_name LIKE "latheef" OR ContactStreams.name LIKE "latheef" OR Campaigns.campaign_name LIKE "latheef" OR Schedules.name LIKE "latheef" OR Streams.type LIKE "latheef" OR Streams.message_from LIKE "latheef"
        )
    )
ORDER BY
    Streams.id
DESC
LIMIT 25 OFFSET 0


//view query

 SELECT StreamViews.lang AS StreamViews__lang FROM stream_views StreamViews WHERE ((StreamViews.contact_number like '%latheef%' OR StreamViews.profile_name like '%latheef%' OR StreamViews.contact_name like '%latheef%' OR StreamViews.campaign_name like '%latheef%' OR StreamViews.schedule_name like '%latheef%' OR StreamViews.type like '%latheef%' OR StreamViews.message_from like '%latheef%' AND StreamViews.account_id = 1) ORDER BY StreamViews.id desc LIMIT 25 OFFSET 0