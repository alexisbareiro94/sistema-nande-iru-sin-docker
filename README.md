1. **Clonar el repositorio**        

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
