https://docs.keydb.dev/docs/about
# KeyDB with Docker
## Migration from Redis

Connect to your keydb server `new-keydb-server-1`

```bash
keydb-cli -h new-keydb-server-1
```

Replicate `old-redis-server` to your `new-keydb-server-1` 

```
replicaof old-redis-server 6379
```

See that the replication is done on the `new-keydb-server-1` logs.

```
replicaof no one
```