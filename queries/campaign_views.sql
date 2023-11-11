CREATE VIEW campaign_views AS SELECT
    campaigns.*,
    templates.name as template,
    templates.status,
    
    users.name AS user
FROM
    campaigns
LEFT JOIN templates ON campaigns.template_id = templates.id
LEFT JOIN users ON campaigns.user_id = users.id
ORDER BY
    `templates`.`name`
DESC

