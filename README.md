# Instructions 

## Remote with TLS
since atheme doesn't support TLS uplinks, (https://github.com/atheme/atheme/issues/265) stunnel has to be used for remote TLS 
encryption (this assumes stunnel is running on the hub already):
- `cd stunnel/`
- Copy `stunnel.conf.example` to `stunnel.conf` then edit
- retrieve certificates from CA `see inspircd hub CA setup with easyrsa3`, place `ca.crt` in `ssl/`
- `docker-compose up -d`

## docker-compose 
- Copy `config.env.example` to `config.env` and edit
- Copy `data/include.default.conf` to `data/include.conf` and edit
- `docker-compose build`
- `docker-compose up -d`

## Administration
- To enable debugging add `-d` to `DAEMON_FLAGS` in `config.env`
- `docker-compose up -d`
- `docker logs -f atheme-atheme-1`
### Anope migration 
See `tools/` for `anope2atheme.php`. YMMV
