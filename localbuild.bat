docker build -t registry.egrand.in/wapi:latest --build-arg BUILD_DATE="$(Get-Date -UFormat '%Y-%m-%dT%H:%M:%SZ')"  --no-cache .
docker-compose -p docker-mysql up