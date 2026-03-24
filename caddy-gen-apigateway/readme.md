# Caddy Gen API Gateway

Docker Compose สำหรับ API Gateway ด้วย Caddy พร้อม Authentication ผ่าน Keycloak

## สถาปัตยกรรมระบบ

### Network Architecture

- **proxy-network** (external) - สำหรับ services ที่ต้องการเข้าถึงจากภายนอก (frontend)
- **backend-network** (bridge) - สำหรับการสื่อสารภายใน backend เท่านั้น

### Services

- **caddy-gen-apigateway** - Reverse proxy และ API Gateway (proxy-network + backend-network)
- **mariadb** - Database สำหรับ Keycloak (backend-network เท่านั้น)
- **authserver** - Keycloak SSO Authentication (proxy-network + backend-network)
- **api** - Backend API Service (proxy-network + backend-network)
- **web1, web2** - Frontend Applications (proxy-network เท่านั้น)

## การติดตั้งและใช้งาน

### 1. สร้าง External Network

```bash
docker network create proxy-network
docker network create backend-network

```

### 2. แก้ไข Configuration

แก้ไขค่าใน `docker-compose.yml`:

- เปลี่ยน `yourdomain.com` เป็น domain ของคุณ
- แก้ไขค่า `KEYCLOAK_ADMIN` และ `KEYCLOAK_ADMIN_PASSWORD`
- แก้ไขค่า database credentials หากต้องการ
- แก้ไข client ID และ realm ให้ตรงกับการตั้งค่า Keycloak

### 3. สร้างและรัน Services

```bash
# Build และรันทุก services
docker-compose up -d

# ตรวจสอบสถานะ
docker-compose ps

# ดู logs
docker-compose logs -f
```

### 4. ตั้งค่า Keycloak

1. เข้าถึง Keycloak Admin: https://sso.yourdomain.com/auth/admin
2. Login ด้วย `adminkeycloak` / `keycloak@passwordadmin7729`
3. สร้าง Realm: `your-app-realm`
4. สร้าง Clients:
   - `your-appclient-web1` (สำหรับ web1)
   - `your-appclient-web2` (สำหรับ web2)
   - `your-app-clientid` (สำหรับ API)

### 5. การเข้าถึง Services

- **Web1**: https://web1.yourdomain.com
- **Web2**: https://web2.yourdomain.com
- **API**: https://yourapi.yourdomain.com
- **SSO**: https://sso.yourdomain.com
- **Caddy Admin**: http://localhost:2015

## Configuration หลัก

### Environment Variables

- `TZ=Asia/Bangkok` - Timezone
- `NODE_ENV=production` - Node.js environment
- Database credentials สำหรับ MariaDB
- Keycloak configuration สำหรับ SSO

### Labels สำหรับ Caddy Gen

Services ที่ต้องการให้ Caddy จัดการต้องมี labels:

```yaml
labels:
  - "virtual.host=yourhost.yourdomain.com"
  - "virtual.port=8080"
  - "virtual.tls=internal"
  - "virtual.cors.enabled=true"
  - "auth.server=sso.yourdomain.com"
  - "auth.realm=your-app-realm"
  - "auth.clientid=your-client-id"
  - "global.cloudflare=true"
```

## การจัดการ

### Stop Services

```bash
docker-compose down
```

### Update Services

```bash
docker-compose pull
docker-compose up -d
```

### View Logs

```bash
# ดู logs ทั้งหมด
docker-compose logs

# ดู logs ของ service ที่กำหนด
docker-compose logs caddy-gen-apigateway
docker-compose logs authserver
docker-compose logs api
```

### Backup Database

```bash
docker-compose exec mariadb mysqldump -u root -p keycloak > backup.sql
```

## ความปลอดภัย

- Database (MariaDB) อยู่ใน backend-network เท่านั้น ไม่สามารถเข้าถึงจากภายนอก
- Frontend services (web1, web2) ใช้เฉพาะ proxy-network
- API Gateway และ Auth Server เชื่อมต่อทั้งสอง networks
- SSL/TLS จัดการโดย Caddy อัตโนมัติ (internal certificates)

## Troubleshooting

### ตรวจสอบ Network

```bash
docker network ls
docker network inspect proxy-network
docker network inspect caddy-gen-apigateway_backend-network
```

### ตรวจสอบ Service Connectivity

```bash
# ทดสอบการเชื่อมต่อระหว่าง containers
docker-compose exec caddy-gen-apigateway ping mariadb
docker-compose exec web1 ping api
```

### ตรวจสอบ Caddy Configuration

```bash
docker-compose exec caddy-gen-apigateway cat /etc/caddy/Caddyfile
```

## Development

สำหรับการพัฒนา สามารถแก้ไข:

- `./web1/` และ `./web2/` สำหรับ frontend applications
- `./api/` สำหรับ backend API (ถ้ามี)
- Environment variables ใน `docker-compose.yml`

## License

MIT License
