create view rating_views as
SELECT ratings.*,
streams.contact_stream_id,
streams.category,
streams.sent_time,
streams.conversationid,
streams.type,
contact_streams.contact_number
from ratings
LEFT join streams on streams.id=ratings.stream_id
left JOIN contact_streams on streams.contact_stream_id=contact_streams.id  
ORDER BY `contact_streams`.`contact_number` DESC;