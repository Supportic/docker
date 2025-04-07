#!/bin/sh

CERTDIR="${HOME}/certs"
BINDIR="${HOME}/bin"

function downloadWindows(){
  curl -fsSL --retry 3 "https://github.com/FiloSottile/mkcert/releases/download/v${MKCERT_VER}/mkcert-v${MKCERT_VER}-windows-amd64.exe" -o "$BINDIR/mkcert.exe" && chmod +x "$BINDIR/mkcert.exe"
}
function downloadMac(){
   curl -fsSL --retry 3 "https://github.com/FiloSottile/mkcert/releases/download/v${MKCERT_VER}/mkcert-v${MKCERT_VER}-darwin-amd64" -o "$BINDIR/mkcert_mac" && chmod +x "$BINDIR/mkcert_mac"
}
function downloadLinux(){
   curl -fsSL --retry 3 "https://github.com/FiloSottile/mkcert/releases/download/v${MKCERT_VER}/mkcert-v${MKCERT_VER}-linux-amd64" -o "$BINDIR/mkcert" && chmod +x "$BINDIR/mkcert"
}

function downloadAll(){
  downloadWindows
  downloadLinux
  downloadMac
}

# clear cert files
rm -rf "${CERTDIR}"/*\.pem

./mkcert -install
# copy certificate authority pem
CAPATH=$(./mkcert -CAROOT)
cp "${CAPATH}/${CAFILE}" "${CERTDIR}/${CAFILE}"

./mkcert -cert-file "${CERTDIR}/${CERTFILE}" -key-file "${CERTDIR}/${KEYFILE}" ${DOMAINS}

# clear binary files which starts with mkcert
rm -f "${BINDIR}"/mkcert*

if [ -z "${HOSTOS}" ]; then
  echo "HOST OS not defined"
  downloadAll
elif [[ "${HOSTOS}" =~ ^win$ ]]; then
   downloadWindows
elif [[ "${HOSTOS}" =~ ^mac$ ]]; then
   downloadMac
elif [[ "${HOSTOS}" =~ ^linux$ ]]; then
   downloadLinux
else
  echo "HOST OS undefined"
fi
