create view  schedule_views as  SELECT
    schedules.*,
    templates.name as template,
    templates.status as template_status,
    campaigns.campaign_name,
    campaigns.start_date,
    campaigns.end_date,
    users.name as user
FROM
    schedules
    left join campaigns on campaigns.id = schedules.campaign_id
    left join users on users.id=schedules.user_id
    left join templates on templates.id=campaigns.template_id