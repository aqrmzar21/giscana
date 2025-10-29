# Manual setup instructions for AdminLTE

1. Download AdminLTE 3.2.0:
   - Go to https://github.com/ColorlibHQ/AdminLTE/releases/download/v3.2.0/AdminLTE-3.2.0.zip
   - Extract the zip file

2. Download Font Awesome Free 6.x:
   - Go to https://fontawesome.com/download
   - Download the "Free for Web" version

3. Copy the files to your project:
   ```bash
   # Create required directories
   mkdir -p public/vendor/adminlte/dist
   mkdir -p public/vendor/fontawesome-free
   mkdir -p public/vendor/jquery
   mkdir -p public/vendor/bootstrap

   # Copy files from AdminLTE zip:
   cp -r AdminLTE-3.2.0/dist/* public/vendor/adminlte/dist/
   cp -r AdminLTE-3.2.0/plugins/jquery/jquery.min.js public/vendor/jquery/
   cp -r AdminLTE-3.2.0/plugins/bootstrap public/vendor/

   # Copy Font Awesome files:
   cp -r fontawesome-free-X.X.X-web/* public/vendor/fontawesome-free/
   ```

4. Verify installation:
   - Check that these files exist:
     - public/vendor/adminlte/dist/css/adminlte.min.css
     - public/vendor/adminlte/dist/js/adminlte.min.js
     - public/vendor/fontawesome-free/css/all.min.css
     - public/vendor/jquery/jquery.min.js
     - public/vendor/bootstrap/js/bootstrap.bundle.min.js

5. Update your layout file if needed:
   The layout file is already set up at: `resources/views/layouts/admin.blade.php`

6. Test the installation:
   ```bash
   php artisan serve
   ```
   Visit http://localhost:8000/dashboard to verify the AdminLTE theme is working correctly.