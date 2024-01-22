Gafana Dashboard ID:

Caddy exporter  14280
Docker and system monitoring 893
Docker monitoring 193
Node Exporter Full 1860
Node Exporter Full with Node Name 10242 


dashboard.json  for import custom dashboard

daemon.json    
{
  "metrics-addr" : "0.0.0.0:9323",
  "experimental" : true
}
systemctl restart docker 
systemctl status docker

used with docker daemon.json
