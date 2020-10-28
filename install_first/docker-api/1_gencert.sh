# sudo -s
export HOST=your_dns_host.com  #map your ip with name
# mkdir -p /root/.docker && cd /root/.docker
cd ./server
# generate CA private and public keys

# ถ้าใน office มีหลายเครื่อง ควรใช้ ca-key.pem ตัวเดียวเพื่อความง่ายในการใช้งาน ไม่ต้องมี หลายขุด
openssl genrsa -aes256 -out ca-key.pem 4096 

openssl req -subj "/CN=$HOST" -new -x509 -days 365 -key ca-key.pem -sha256 -out ca.pem

# create a server key and certificate signing request (CSR). 
# Make sure that “Common Name” matches the hostname you use to connect to Docker
openssl genrsa -out server-key.pem 4096
openssl req -subj "/CN=$HOST" -sha256 -new -key server-key.pem -out server.csr

# sign the public key with our CA
# Since TLS connections can be made through IP address as well as DNS name, 
# the IP addresses can also be specified when creating the certificate
echo subjectAltName = DNS:$HOST,IP:127.0.0.1 >> extfile.cnf

# Set the Docker daemon key’s extended usage attributes to be used only 
# for server authentication
echo extendedKeyUsage = serverAuth >> extfile.cnf

# Now, generate the signed certificate
openssl x509 -req -days 365 -sha256 -in server.csr -CA ca.pem -CAkey ca-key.pem \
  -CAcreateserial -out server-cert.pem -extfile extfile.cnf

# For client authentication, create a client key and certificate signing request
openssl genrsa -out key.pem 4096
openssl req -subj '/CN=client' -new -key key.pem -out client.csr

# To make the key suitable for client authentication, create a new extensions config file
echo extendedKeyUsage = clientAuth > extfile-client.cnf

# Now, generate the signed certificate
openssl x509 -req -days 365 -sha256 -in client.csr -CA ca.pem -CAkey ca-key.pem \
  -CAcreateserial -out cert.pem -extfile extfile-client.cnf

# Cleanup
rm -v client.csr server.csr extfile.cnf extfile-client.cnf
chmod -v 0400 ca-key.pem key.pem server-key.pem
chmod -v 0444 ca.pem server-cert.pem cert.pem

