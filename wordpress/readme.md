# Wordpress Playground

Copy `.env.sample` -> `.env` and adjust variables.
Copy `.db.env.sample` -> `.db.env` and adjust variables.

http://localhost
http://localhost/wp-admin

**install**  
`make install`

**start**
`make start`

**enter container**  
`make shell`

**enter wpcli container**
`make wpcli`

**remove everything**  
`make erase`

## Info

The following env vars are already defined and enabled:

| ENV              | VAL  | DESCR                              |
| ---------------- | ---- | ---------------------------------- |
| WP_DEBUG_LOG     | true |                                    |
| SCRIPT_DEBUG     | true |                                    |
| SAVEQUERIES      | true |                                    |
| WP_DEBUG_DISPLAY | true | inserts white bar above menu in BE |
