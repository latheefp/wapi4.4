@echo off

REM Login to Docker Hub
docker login -u latheefp -p Chimmu@123

REM Push the image
REM docker push registry.egrand.in/wapi:latest
docker push latheefp/wapi:latest

