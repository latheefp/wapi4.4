//unbilled stream.conversation count //should be zero.
SELECT
    COUNT(streams.id)
FROM
    streams 
WHERE
    streams.tmp_upate_json LIKE "%%pricing%%" AND streams.conversationid NOT IN(
    SELECT
        ratings.conversation
    FROM
        ratings
);

#fix table to rerun.

update streams set streams.rated=0

WHERE
    streams.tmp_upate_json LIKE "%%pricing%%" AND streams.conversationid NOT IN(
    SELECT
        ratings.conversation
    FROM
        ratings
);



///unbilled count.

SELECT
    COUNT(streams.id)
FROM
    streams AS Streams
WHERE
    streams.tmp_upate_json LIKE "%%pricing%%" AND id NOT IN(
    SELECT
        ratings.stream_id
    FROM
        ratings
) AND streams.type IN('send', 'api', 'camp') AND streams.success = TRUE;


//get all rated stream id which missing from ratings table. 
SELECT count(streams.id)
FROM streams AS Streams
LEFT JOIN ratings ON streams.id = ratings.stream_id
WHERE streams.tmp_upate_json LIKE '%%pricing%%'
    AND streams.type IN ('send', 'api', 'camp')
    AND streams.success = TRUE
    AND ratings.stream_id IS NULL
    and streams.rated=1


//diffrent between what rated instreams and what is not in ratings tables. gap should be zero. 
SELECT
    COUNT(streams.id)
FROM
    streams AS Streams
WHERE
    streams.rated = 1 AND streams.conversationid NOT IN(
    SELECT
        conversation
    FROM
        ratings
);

//fix for it 

update streams set streams.rated=0 WHERE streams.rated = 1 AND streams.conversationid NOT IN( SELECT conversation FROM ratings ); 


#the streams still pendng for rating where json is update. zhould be zero.
SELECT
    COUNT(streams.id)
FROM
    streams
WHERE
    streams.rated = 0 AND streams.timestamp IS NOT NULL;