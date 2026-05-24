# Caddy-Gen Label Options Reference

Template นี้ใช้สำหรับ generate Caddyfile จาก Docker container labels

---

## 1. Global Labels

| Label               | คำอธิบาย                           | ค่าตัวอย่าง |
| ------------------- | ---------------------------------- | ----------- |
| `global.cloudflare` | เปิดใช้ Cloudflare trusted proxies | `"true"`    |W

---

## 2. Virtual Host Labels (หลัก)

| Label               | คำอธิบาย                                 | ค่าตัวอย่าง           |
| ------------------- | ---------------------------------------- | --------------------- |
| `virtual.host`      | **ชื่อ domain** (บังคับ)                 | `"api.example.com"`   |
| `virtual.port`      | พอร์ตของ container (default: 80)         | `"3000"`              |
| `virtual.alias`     | domain alias ที่จะ redirect ไป host หลัก | `"www.example.com"`   |
| `virtual.tls-email` | email สำหรับ Let's Encrypt               | `"admin@example.com"` |
| `virtual.tls`       | TLS config (ถ้าไม่ใช้ email)             | `"internal"`          |

---

## 3. Basic Auth Labels

| Label                   | คำอธิบาย                   | ค่าตัวอย่าง    |
| ----------------------- | -------------------------- | -------------- |
| `virtual.auth.username` | username สำหรับ basic auth | `"admin"`      |
| `virtual.auth.password` | password (hashed)          | `"$2a$14$..."` |
| `virtual.auth.path`     | path ที่ต้องการ protect    | `"/admin/*"`   |

---

## 4. JWT/Auth Labels (Keycloak)

| Label               | คำอธิบาย               | ค่าตัวอย่าง           |
| ------------------- | ---------------------- | --------------------- |
| `auth.validatejwt`  | เปิดใช้ JWT validation | `"true"`              |
| `auth.server`       | Keycloak server        | `"auth.example.com"`  |
| `auth.realm`        | Keycloak realm         | `"myrealm"`           |
| `auth.roles`        | roles ที่ต้องการ       | `"admin user"`        |
| `auth.clientid`     | client ID              | `"my-app"`            |
| `auth.public_paths` | paths ที่ไม่ต้อง auth  | `"/health /public/*"` |

**หรือใช้แบบ manual:**

| Label                      | คำอธิบาย       | ค่าตัวอย่าง                                                               |
| -------------------------- | -------------- | ------------------------------------------------------------------------- |
| `virtual.jwt.issuer`       | JWT issuer URL | `"https://auth.example.com/realms/myrealm"`                               |
| `virtual.jwt.jwks_uri`     | JWKS endpoint  | `"https://auth.example.com/realms/myrealm/protocol/openid-connect/certs"` |
| `virtual.jwt.audience`     | audience       | `"myrealm"`                                                               |
| `virtual.jwt.require_role` | required roles | `"admin"`                                                                 |

---

## 5. Rate Limit Labels

| Label                      | คำอธิบาย                    | ค่าตัวอย่าง                  |
| -------------------------- | --------------------------- | ---------------------------- |
| `virtual.ratelimit.zone`   | ชื่อ zone (บังคับถ้าจะใช้)  | `"api_limit"`                |
| `virtual.ratelimit.key`    | key สำหรับ rate limit       | `"{http.request.remote_ip}"` |
| `virtual.ratelimit.events` | จำนวน events (default: 100) | `"50"`                       |
| `virtual.ratelimit.window` | time window (default: 1m)   | `"1m"`                       |

---

## 6. CORS & Debug Labels

| Label                  | คำอธิบาย                            | ค่าตัวอย่าง |
| ---------------------- | ----------------------------------- | ----------- |
| `virtual.cors.enabled` | เปิดใช้ CORS headers                | `"true"`    |
| `virtual.debug`        | เปิด debug mode (log level = DEBUG) | `"true"`    |
| `virtual.log.level`    | กำหนด log level                     | `"INFO"`    |

---

## 7. Advanced/Custom Labels

| Label                      | คำอธิบาย                                    | ค่าตัวอย่าง            |
| -------------------------- | ------------------------------------------- | ---------------------- |
| `virtual.host.directives`  | custom directives ใน host block             | `"root * /var/www"`    |
| `virtual.host.import`      | import snippet                              | `"my_snippet"`         |
| `virtual.proxy.matcher`    | matcher สำหรับ reverse_proxy                | `"@api"`               |
| `virtual.proxy.directives` | custom directives ใน proxy                  | `"health_uri /health"` |
| `virtual.proxy.lb_policy`  | load balancer policy (default: round_robin) | `"least_conn"`         |
| `virtual.proxy.import`     | import snippet ใน proxy                     | `"proxy_snippet"`      |

---

## ตัวอย่าง Docker Compose

### ตัวอย่าง 1: Basic - ไม่มี auth

```yaml
services:
  web-simple:
    image: nginx:alpine
    labels:
      - "virtual.host=web.example.com"
      - "virtual.port=80"
      - "virtual.tls-email=admin@example.com"
```

### ตัวอย่าง 2: Basic Auth

```yaml
services:
  admin-panel:
    image: myapp:latest
    labels:
      - "virtual.host=admin.example.com"
      - "virtual.port=3000"
      - "virtual.tls-email=admin@example.com"
      - "virtual.auth.username=admin"
      - "virtual.auth.password=$$2a$$14$$hashedpassword"
      - "virtual.auth.path=/admin/*"
```

### ตัวอย่าง 3: JWT Auth (Keycloak) + Rate Limit + CORS

```yaml
services:
  api-service:
    image: api:latest
    labels:
      - "virtual.host=api.example.com"
      - "virtual.port=8080"
      - "virtual.tls-email=admin@example.com"
      # JWT/Keycloak
      - "auth.validatejwt=true"
      - "auth.server=auth.example.com"
      - "auth.realm=myrealm"
      - "auth.roles=api-user"
      - "auth.public_paths=/health /public/*"
      # Rate Limit
      - "virtual.ratelimit.zone=api_zone"
      - "virtual.ratelimit.events=100"
      - "virtual.ratelimit.window=1m"
      # CORS
      - "virtual.cors.enabled=true"
```

### ตัวอย่าง 4: Alias + Debug Mode

```yaml
services:
  app-with-alias:
    image: myapp:latest
    labels:
      - "virtual.host=app.example.com"
      - "virtual.alias=www.app.example.com"
      - "virtual.port=3000"
      - "virtual.tls-email=admin@example.com"
      - "virtual.debug=true"
```

### ตัวอย่าง 5: Cloudflare Proxy (global)

```yaml
services:
  cloudflare-app:
    image: myapp:latest
    labels:
      - "global.cloudflare=true"
      - "virtual.host=cf.example.com"
      - "virtual.port=80"
```

### ตัวอย่าง 6: Full Featured (ครบทุก option)

```yaml
services:
  full-app:
    image: myapp:latest
    labels:
      # Host Configuration
      - "virtual.host=app.example.com"
      - "virtual.alias=www.app.example.com"
      - "virtual.port=8080"
      - "virtual.tls-email=admin@example.com"

      # JWT Authentication (Keycloak)
      - "auth.validatejwt=true"
      - "auth.server=auth.example.com"
      - "auth.realm=production"
      - "auth.roles=app-user admin"
      - "auth.clientid=my-app"
      - "auth.public_paths=/health /api/public/*"

      # Rate Limiting
      - "virtual.ratelimit.zone=app_zone"
      - "virtual.ratelimit.key={http.request.remote_ip}"
      - "virtual.ratelimit.events=200"
      - "virtual.ratelimit.window=1m"

      # CORS
      - "virtual.cors.enabled=true"

      # Proxy Settings
      - "virtual.proxy.lb_policy=least_conn"

      # Logging
      - "virtual.log.level=INFO"
```

---

## สรุปการทำงาน

| Mode           | เงื่อนไข                                             | Features                                                       |
| -------------- | ---------------------------------------------------- | -------------------------------------------------------------- |
| **No Auth**    | ไม่มี auth labels                                    | reverse_proxy + security_headers                               |
| **Basic Auth** | มี `virtual.auth.username` + `virtual.auth.password` | basicauth + reverse_proxy + security_headers                   |
| **JWT Auth**   | มี `auth.validatejwt=true` หรือ `virtual.jwt.*`      | jwtauth + user_headers_to_backend + security_headers_with_cors |

### Features เสริม

- **CORS** → เพิ่ม `virtual.cors.enabled=true`
- **Rate Limit** → เพิ่ม `virtual.ratelimit.zone=xxx`
- **Debug** → เพิ่ม `virtual.debug=true`
- **Cloudflare** → เพิ่ม `global.cloudflare=true`

---

## อธิบายเพิ่มเติม: Rate Limit Zone

### `virtual.ratelimit.zone` คืออะไร?

**`zone`** คือ **ชื่อที่ตั้งเอง** สำหรับระบุกลุ่มของ rate limit rule ไม่ได้หามาจากไหน

### หลักการทำงาน

```
rate_limit {
  zone api_zone {        # ← ชื่อ zone (ตั้งเองได้)
    key {http.request.remote_ip}
    events 100
    window 1m
  }
}
```

- **`zone`** = ชื่อ identifier สำหรับ rate limit rule นั้นๆ
- ใช้แยก rate limit ของแต่ละ service/endpoint ออกจากกัน
- **ตั้งชื่ออะไรก็ได้** ที่สื่อความหมาย เช่น:
  - `api_zone` - สำหรับ API service
  - `login_zone` - สำหรับ login endpoint
  - `upload_zone` - สำหรับ upload service

### ตัวอย่างการใช้งาน

```yaml
# Service 1: API - 100 requests/นาที
api-service:
  labels:
    - "virtual.ratelimit.zone=api_zone"
    - "virtual.ratelimit.events=100"
    - "virtual.ratelimit.window=1m"

# Service 2: Login - 10 requests/นาที (เข้มงวดกว่า)
auth-service:
  labels:
    - "virtual.ratelimit.zone=login_zone"
    - "virtual.ratelimit.events=10"
    - "virtual.ratelimit.window=1m"
```

### สรุป

| คำถาม                | คำตอบ                                               |
| -------------------- | --------------------------------------------------- |
| หามาจากไหน?          | **ตั้งเอง**                                         |
| ตั้งชื่ออะไรได้บ้าง? | อะไรก็ได้ที่เป็น valid identifier                   |
| ทำไมต้องมี zone?     | เพื่อแยก rate limit rule ของแต่ละ service ออกจากกัน |
