<p align="center">ALCOPA Cars auctions parser</p>


## Launching project

1. Run "git clone https://github.com/Pikerstrow/alcopa-parser"
2. Run "composer install"
2. Rename '.env.example' to '.env' and add necessary settings, including your localhost DB connection credentials (IMPORTANT! If key property is empty you need to run 'php artisan key:generate' command)
3. Run 'php artisan migrate:fresh --seed' command.

Note: '--seed' flag is important due to project uses Voyager Admin panel and all necessary settings set via seeders!

4. Access to admin panel:
    url: 'your-test-domain.test/admin';
    login: 'admin@admin.com';
    password: 'password';
    
5. For launching parser all you need is just run 'php artisan parser:parse_alcopa' command :)
    
    
    


