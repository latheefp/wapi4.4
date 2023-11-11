drop view stream_views;
CREATE VIEW stream_views AS SELECT
    streams.*,
    schedules.name AS schedule_name,
    campaigns.id AS compaign_id,
    campaigns.campaign_name,
    contact_streams.contact_number,
    contact_streams.profile_name,
    contact_streams.name as contact_name
FROM
    streams
LEFT JOIN schedules ON schedules.id = streams.schedule_id
LEFT JOIN campaigns ON campaigns.id = schedules.campaign_id
left JOIN contact_streams on contact_streams.id=streams.contact_stream_id