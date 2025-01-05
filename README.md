# Instructions 

## Remote with TLS
since atheme doesn't support TLS uplinks, stunnel has to be used for remote TLS encryption (this assumes stunnel is running on the hub already):
- `cd stunnel/`
- Copy `stunnel.conf.example` to `stunnel.conf` then edit
- retrieve certificates from CA `see inspircd hub CA setup with easyrsa3`
- `docker-compose up -d`

## docker-compose 
- Copy `config.env.example` to `config.env` and edit
- Copy `data/include.default.conf` to `data/include.conf` and edit
- `docker-compose build`
- `docker-compose up -d`

## Anope migration 
See `tools/` for `anope2atheme.php`. YMMV
