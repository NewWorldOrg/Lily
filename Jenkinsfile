pipeline {
  agent any
  stages {
    stage('test') {
      steps {
        sh '''/bin/sh -c set -eux;

PHP_INI_DIR=/usr/local/etc/php;
PHP_CFLAGS="pfstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64";
PHP_CPPFLAGS="-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64";
PHP_LDFLAGS="-Wl,-O1 -pie";
GPG_KEYS="528995BFEDFBA7191D46839EF9BA0ADA31CBD89E 39B641343D8C104B2B146DC3F9C39DC0B9698544 F1F692238FBC1666E5A5CCD4199F9DFEF6FFBAFD";
PHP_VERSION=8.1.10;
PHP_URL=https://www.php.net/distributions/php-8.1.10.tar.xz;
PHP_ASC_URL=https://www.php.net/distributions/php-8.1.10.tar.xz.asc;
PHP_SHA256=90e7120c77ee83630e6ac928d23bc6396603d62d83a3cf5df8a450d2e3070162;

apk add --no-cache ca-certificates	curl tar xz	openssl make;
apk add --no-cache --virtual .fetch-deps gnupg;

mkdir -p "$PHP_INI_DIR/conf.d"; 

mkdir -p /usr/src;
cd /usr/src; 		
curl -fsSL -o php.tar.xz "$PHP_URL";
if [ -n "$PHP_SHA256" ]; then
    echo "$PHP_SHA256 *php.tar.xz" | sha256sum -c -; 	
fi;
if [ -n "$PHP_ASC_URL" ]; then
    curl -fsSL -o php.tar.xz.asc "$PHP_ASC_URL";
    export GNUPGHOME="$(mktemp -d)";
    for key in $GPG_KEYS;
    do
        gpg --batch --keyserver keyserver.ubuntu.com --recv-keys "$key";
   done;
   gpg --batch --verify php.tar.xz.asc php.tar.xz;
   gpgconf --kill all;
   rm -rf "$GNUPGHOME";
fi;
apk del --no-network .fetch-deps

ls -la

apk add --no-cache --virtual .build-deps $PHPIZE_DEPS argon2-dev coreutils curl-dev	gnu-libiconv-dev libsodium-dev libxml2-dev linux-headers oniguruma-dev openssl-dev readline-dev sqlite-dev;
rm -vf /usr/include/iconv.h;
export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS"; 	

cd /usr/src/php;
gnuArch="$(dpkg-architecture --query DEB_BUILD_GNU_TYPE)";
./configure --build="$gnuArch" --with-config-file-path="$PHP_INI_DIR" --with-config-file-scan-dir="$PHP_INI_DIR/conf.d" --enable-option-checking=fatal --with-mhash --with-pic --enable-ftp	--enable-mbstring \\
	--enable-mysqlnd --with-password-argon2	--with-sodium=shared --with-pdo-sqlite=/usr --with-sqlite3=/usr	--with-curl --with-iconv=/usr --with-openssl --with-readline --with-zlib --enable-phpdbg --enable-phpdbg-readline \\
  --with-pear	$(test "$gnuArch" = \'s390x-linux-musl\' && echo \'--without-pcre-jit\');
make -j "$(nproc)";
find -type f -name \'*.a\' -delete;
make install;
find /usr/local -type f -perm \'/0111\'	-exec sh -euxc \'strip --strip-all "$@" || : \' -- \'{}\' + ;
make clean;
cp -v php.ini-* "$PHP_INI_DIR/";
cd /;
docker-php-source delete;
runDeps="$(
    scanelf --needed --nobanner --format \'%n#p\' --recursive /usr/local | tr \',\' \'\\n\' | sort -u | awk \'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }\' 	
    )"; 	
apk add --no-cache $runDeps;
apk del --no-network .build-deps;
pecl update-channels;
rm -rf /tmp/pear ~/.pearrc;
php --version
'''
      }
    }

  }
}