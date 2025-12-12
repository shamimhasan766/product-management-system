---- Installation proccess----

-- To install on locally first pc need installed php and composer 
1. Clone the project and remove .example from .env.example if you dont have installed mysql than use sqlite
2. run composer install , composer dump-autoload , npm install, npm run build, php artisan key:generate, php artisan migrate on terminal
3. run php artisan serve and you will see local link for preview.
4. go to the link register with name, email, password and on the dashboard you will see product list and create update delete product. and the second tab you will see trashed products and can restore and permanent delete.

---- Question answser ----

1. in dependency needs to inject in method or constructor function and service container create instance of the class. In Facade give shortcut to call class method no need to inject. Facade make the instance behind the scene.
   
2. For optimized search query needs to take multiple steps like :
   1. Select the columns which need instead of take all the columns
   2. use eagar loading for relations
   3. use caching for same query.
   4. pagination (whithout load all product load I can use 10 product per page)
   5. proper database indexing
    
3. Laravel CSRF protection use in inertia same as blade template. inertia by default use axios and sends csrf token automatically.

4. N + 2 problem is suppose I need to show products with categories.. but categories is in other table .. If i use foreach for every product it will make query for every loop and it will performance issue for big dataset. but if i use egar loading this will not create N + 1 problem
