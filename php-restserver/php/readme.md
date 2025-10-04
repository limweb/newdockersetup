# Restful Web Server Api v0.0.1

## Gen Private and Public key with openssl
    openssl genrsa -out rsa.private 2048
    openssl rsa -in rsa.private -out rsa.public -pubout -outform PEM

## Config
    .env  
    configs/config.php

## eruda
   eruda.init();    
   eruda.add(eruda_vue);

## unocss
    [un-cloak] {
        display: none;
    }
    <div class=" un-cloak "> test </div>
<!-- define unocss options... -->
<script>
  window.__unocss = {
    rules: [
      // custom rules...
    ],
    presets: [
      // custom presets...
    ],
    // ...
  }
</script>
<!-- ... and then load the runtime -->
<script src="https://cdn.jsdelivr.net/npm/@unocss/runtime"></script>    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@unocss/reset/normalize.min.css" />