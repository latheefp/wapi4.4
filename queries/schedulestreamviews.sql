create view schedulestreamsviews  as SELECT * from streams where streams.schedule_id IS NOT null  
ORDER BY `streams`.`phonenumberid`  DESC