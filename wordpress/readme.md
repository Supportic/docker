# Wordpress Playground

Removing or stopping the containers will not remove the source code. Only when the volume gets deleted.

http://localhost

**start**  
`docker compose up -d wordpress db adminer`

**enter container**  
`docker compose exec wordpress sh`

**enter wpcli container**
`docker compose run --rm wpcli sh`

**execute wpcli commands**
`docker compose run --rm wpcli wp ...`

**remove everything**  
`docker compose down -v --rmi all`
