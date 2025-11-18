🧪 **Usuario de prueba**

Podés ingresar al sistema con las siguientes credenciales:

### 🚀 Demo en línea  
👉 [Ñande iru](https://nande-iru.on-forge.com/)  
*(Acceso directo: [https://nande-iru.on-forge.com/login](https://nande-iru.on-forge.com/login))*

### 🔐 Usuario de prueba  
- **Email:** `test@example.com`  
- **Contraseña:** `Test.1234`

### Stack usado  
- PHP 8.4.13  
- Laravel 12  
- MySQL 8.0.43  
- Redis-cli 7.0.15  
- Node.js 22.15.0 y npm 10.9.2  

### Instalación y configuración  

1. **Clonar el repositorio**  
   > ⚠️ **Importante:** Se recomienda clonar la rama `con-reverb` para tener todas las funcionalidades completas.  
   
   - **SSH:** `git clone -b con-reverb git@github.com:alexisbareiro94/sistema-nande-iru-sin-docker.git`  
   - **HTTP:** `git clone -b con-reverb https://github.com/alexisbareiro94/sistema-nande-iru-sin-docker.git`

2. **Instalar dependencias**  
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configurar las variables de entorno**  
   ```bash
   cp .env.example .env
   ```

4. **Generar la APP_KEY**  
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones y seeders**  
   ```bash
   php artisan migrate --seed
   ```

6. **Iniciar el servidor de desarrollo**  
   ```bash
   composer run dev
   ```

---

![Mi proyecto](https://i.imgur.com/7MorZDR.png)

![Mi proyecto](https://i.imgur.com/8Bquh65.png)

![Mi proyecto](https://i.imgur.com/w9o7ykq.png)

![Mi proyecto](https://i.imgur.com/SqSlf3r.png)

![Mi proyecto](http://i.imgur.com/yJkhPbq.png)

![Mi proyecto](https://i.imgur.com/P0014rw.png)

![Mi proyecto](https://i.imgur.com/5bj7wPv.png)

![Mi proyecto](https://i.imgur.com/GSErzIo.png)

![Mi proyecto](https://i.imgur.com/98csaWA.png)

![Mi proyecto](https://i.imgur.com/FO7VCar.png)

![Mi proyecto](https://i.imgur.com/xGWUDnN.png)
# sistema-nande-iru-sin-docker
