ARG UBUNTU_VERSION="noble"

FROM ubuntu:${UBUNTU_VERSION}

ARG BUILD_SERVER_NAME="services.lame-network.local"

RUN apt -y update

RUN apt -y install coreutils perl git automake autoconf build-essential libpcre2-dev rapidjson-dev libcurl4-gnutls-dev libargon2-dev libmaxminddb-dev libldap2-dev rapidjson-dev libmysqlclient-dev libmysqlclient-dev default-libmysqlclient-dev libpq-dev libre2-dev gnutls-dev libsqlite3-dev libmbedtls-dev libqrencode-dev libpcre3-dev libtre-dev pkg-config libwww-perl libidn-dev libpasswdqc-dev libcrack2-dev libperl-dev libsodium-dev cracklib-runtime libcrypt-cracklib-perl sendmail gettext

RUN groupadd atheme

RUN useradd --system --shell /bin/bash atheme -g atheme

WORKDIR /tmp

RUN git clone https://github.com/atheme/atheme.git -b master

WORKDIR /tmp/atheme

RUN git submodule update --init --recursive

RUN ./configure --prefix=/usr/local --enable-large-net --enable-contrib --enable-legacy-pwcrypto --enable-contrib

RUN make -j$(nproc) 

RUN make install

RUN mkdir -p /etc/atheme -p /etc/ssl/atheme -p /var/log/atheme 

RUN mv /usr/local/etc /usr/local/etc_old

RUN ln -sf /etc/atheme /usr/local/etc

ADD atheme.conf /etc/atheme

ADD include.default.conf /etc/atheme/include.conf

RUN chown -R atheme:atheme /etc/atheme /etc/ssl/atheme /var/log/atheme

WORKDIR /

USER atheme 

RUN /usr/local/bin/atheme-services -b ; true

VOLUME /etc/atheme

VOLUME /etc/ssl/atheme

VOLUME /var/log/atheme

ENTRYPOINT ["/usr/local/bin/atheme-services", "-p", "/tmp/atheme.pid", "-n"]
