virtual.host domain name, don't pass http:// or https://, you can separate them with space,
virtual.alias domain alias, e.q. www prefix,
virtual.port port exposed by container, e.g. 3000 for React apps in development,
virtual.tls-email the email address to use for the ACME account managing the site's certificates,
virtual.auth.path with
virtual.auth.username and
virtual.auth.password together provide HTTP basic authentication.
Password should be a string base64 encoded from bcrypt hash. You can use https://bcrypt-generator.com/ with default config and https://www.base64encode.org/.