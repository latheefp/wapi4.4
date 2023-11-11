create view apiviews as 
SELECT api_keys.*,
users.username
from api_keys
LEFT JOIN users on api_keys.user_id=users.id
