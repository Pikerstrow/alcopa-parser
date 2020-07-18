<p align="center">ALCOPA Cars auctions parser</p>


## Launching project

1. Run "git clone https://github.com/Pikerstrow/alcopa-parser"
2. Go to project directory and run "composer install"
3. Rename '.env.example' to '.env' and add necessary settings, including your localhost DB connection credentials (IMPORTANT! If key property is empty you need to run 'php artisan key:generate' command)
4. Run 'php artisan migrate:fresh --seed' command.

Note: '--seed' flag is important due to project uses Voyager Admin panel and all necessary settings set via seeders!

5. Run 'php artisan storage:link'

6. Access to admin panel:

    url: 'your-test-domain.test/admin';
    
    login: 'admin@admin.com';
    
    password: 'password';
    
7. In admin panel you will see orangle box with information about necessary links andsense. Just click Fix It.    
8. For launching parser all you need is just run 'php artisan parser:parse_alcopa' command :)

## IMPORTANT NOTE. Sometimes car images aren't displaying in cars table or car details view after parsing / partial parsing (it looks like link to image is broken). Just run 'php artisan storage:link' one more time.
    
    
    


