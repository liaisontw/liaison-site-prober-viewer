#!/usr/bin/env bash
set -e

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=$4
WP_VERSION=${5:-latest}

WP_CORE_DIR=/tmp/wordpress
WP_TESTS_DIR=/tmp/wordpress-tests-lib

download() {
  curl -sL "$1" -o "$2"
}

echo "### Download WordPress Core"

mkdir -p "$WP_CORE_DIR"

if [ "$WP_VERSION" = "latest" ]; then
  WP_TARBALL="https://wordpress.org/latest.tar.gz"
  TESTS_TAG="trunk"
else
  WP_TARBALL="https://wordpress.org/wordpress-${WP_VERSION}.tar.gz"
  TESTS_TAG="tags/${WP_VERSION}"
fi

download "$WP_TARBALL" /tmp/wordpress.tar.gz
tar -xzf /tmp/wordpress.tar.gz --strip-components=1 -C "$WP_CORE_DIR"

echo "### Download WP test suite (NO SVN)"

mkdir -p "$WP_TESTS_DIR"

download "https://develop.svn.wordpress.org/${TESTS_TAG}/tests/phpunit/includes.zip" /tmp/includes.zip
download "https://develop.svn.wordpress.org/${TESTS_TAG}/tests/phpunit/data.zip" /tmp/data.zip

unzip -q /tmp/includes.zip -d "$WP_TESTS_DIR"
unzip -q /tmp/data.zip -d "$WP_TESTS_DIR"

download \
  "https://develop.svn.wordpress.org/${TESTS_TAG}/wp-tests-config-sample.php" \
  "$WP_TESTS_DIR/wp-tests-config.php"

sed -i "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR/':" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/localhost/$DB_HOST/" "$WP_TESTS_DIR/wp-tests-config.php"

echo "✓ WP tests installed (no svn)"
