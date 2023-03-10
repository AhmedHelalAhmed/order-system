## About order system

In a system that has three main models; Product, Ingredient, and Order.
A Burger (Product) may have several ingredients:

- 150g Beef
- 30g Cheese
- 20g Onion

The system keeps the stock of each of these ingredients stored in the database. You
can use the following levels for seeding the database:

- 20kg Beef
- 5kg Cheese
- 1kg Onion
  When a customer makes an order that includes a Burger. The system needs to update the
  stock of each of the ingredients so it reflects the amounts consumed.
  Also when any of the ingredients stock level reaches 50%, the system should send an
  email message to alert the merchant they need to buy more of this ingredient.
  Requirements:
  First, Write a controller action that:

1. Accepts the order details from the request payload.
2. Persists the Order in the database.
3. Updates the stock of the ingredients.
   Second, ensure that en email is sent once the level of any of the ingredients reach
   below 50%. Only a single email should be sent, further consumption of the same
   ingredient below 50% shouldn't trigger an email.
   Finally, write several test cases that assert the order was correctly stored and the
   stock was correctly updated.
   The incoming payload may look like this:

```   
{
   "products": [
       {
           "product_id": 1,
           "quantity": 2,
       }
   ]
}
```

## [Postman collection](https://www.postman.com/ahmedhelalahmed/workspace/ahmed-helal/collection/3913416-5b97c36f-5975-47f6-b42e-6bbe61533694?action=share&creator=3913416)

## Technology and tools
- PHP Laravel - mysql - redis - queue and event - docker 

## The main Idea:
- Database transaction to rollback if there is an error happened
- Event fired (IngredientsReachBelowPercentage) to notify the merchant when the stock reach below 50% of the level for ingredient with queue listener (NotifyMerchant) using redis
- Integration and unit tests
- You should first generate token from (Generate bearer tokens API) and send it in order API check postman collection
- Note: in postman once you hit Generate bearer tokens API it should bearer token automatically if not please add in in authentication bearer method for Store order API
## Docker environment steps to set up the projects
- Clone the project then open terminal inside the project directory and run
- ``` cp .env.example .env ```
- ```docker-compose up -d --build```
- ```docker-compose run --rm composer install```
- ```docker-compose run artisan key:generate```
- ```docker-compose run artisan migrate --seed```
- ```docker-compose run artisan test``` To run tests
- ```docker-compose run artisan queue:work``` For worker to run and execute listener

## Docker containers
- artisan: to run artisan commands
- composer: to run composer commands
- database server: mariadb database server
- mailhog: for testing and reviewing emails
- php: required for webserver to work with php
- phpmyadmin: ui for database
- redis: in-memory database use here as queue driver
- webserver: nginx

## links for docker in local [Postman collection](https://www.postman.com/ahmedhelalahmed/workspace/ahmed-helal/collection/3913416-5b97c36f-5975-47f6-b42e-6bbe61533694?action=share&creator=3913416)
- [phpmyadmin](http://localhost:8080)
- [mailhog](http://localhost:8025)

## Commands without docker
- Clone the project 
- ``` cp .env.example .env ``` Change database and redis configuration iin .env file with yours
- ```composer install```
- ```php artisan key:generate```
- ```php artisan migrate --seed```
- ```php artisan test``` To run tests
- ```php artisan queue:work``` For worker to run and execute listener and keep it running
- ```php artisan serve``` Open new terminal inside the project directory to execute the built-in web server
