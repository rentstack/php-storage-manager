# PHP Storage Manager

## CLI
### Get data from Redis
```
$ ./command redis get
php command.php redis get
```
### Add data to Redis
```
$ ./command redis add {key} {value}
php command.php redis add {key} {value}
```
### Remove data from Redis
```
$ ./command redis delete {key}
php command.php redis delete {key}
```
### HTTP
```
GET /  Simple HTML client

REST
-------------
GET /api/redis
DELETE /api/redis/{key}
POST /api/redis/add
```