API REST in Laravel 8

DESCRIPTION: Game in which two dice are rolled. If these add up to 7, the game is won, otherwise it is lost.


SAFETY: includes passport AUTHENTICATION in all accesses to microservice URLs (routes protected by middleware).  The ACCESS CONTROL is configured using tokens.  It has a basic ROLE SYSTEM that has been established to determine the access to the different routes.


TESTING: the application includes a test file system for each of the routes, executable in PHPunit (list of topics, checks and results below)


The next API REST endpoints working success has been tested and verified in postman: 

    POST	players                 AuthController::class, 'register'       > creates an user

    POST	players/login	        AuthController::class, 'login'          > user login


Passport protected routes (middleware) for all users:

    POST	players/logout          AuthController::class, 'logout'	        > user logout

    PUT     players/{id}            UserController::class, 'editNickname'	> an user modifies his Nickname

    POST	players/{id}/games      PlayController::class, 'diceRoll'       > an user rolls the dice

    GET     players/{id}/games      PlayController::class, 'getOwnPlays'	> a specific user gets the list of all his plays

    DELETE	players/{id}/games      PlayController::class, 'removeOwnPlays'	> the rolls of a specific user are removed by him

Passport protected routes (middleware) for administrators only:

    GET     players                 UserController::class, 'allPlayersInfo'	> list of all users in the system and their average success rate

    GET     players/ranking         PlayController::class, 'rankingAverage'	> average ranking of all users in the system (average percentage of successes)

    GET     players/ranking/loser   PlayController::class, 'loserPlayer'	> user with worst success rate

    GET     players/ranking/winner	PlayController::class, 'winnerPlayer'	> user with best success rate



PHPunit TESTING SYSTEM:

   PASS  Tests\Feature\AuthTest
   
  ✓ user can register
  
  ✓ user can register empty nickname and nicknamed anonymous
  
  ✓ required email
  
  ✓ required password
  
  ✓ required password confirmation
  
  ✓ unique nickname
  
  ✓ unique email
  
  ✓ user can login
  
  ✓ required email at login
  
  ✓ required password at login
  
  ✓ errors validation login email
  
  ✓ errors validation login password
  
  ✓ auth user can logout
  
   PASS  Tests\Feature\UserTest
   
  ✓ user can be edit nickname
  
  ✓ admin role can list all players info
  
  ✓ player role cannot list all players info
  
  ✓ unauth user cannot list all players info
  
   PASS  Tests\Feature\PlayTest
   
  ✓ auth player can roll dice
  
  ✓ unauth player cannot roll dice
  
  ✓ auth player can get own plays
  
  ✓ unauth player cannot get own plays
  
  ✓ auth player can remove own plays
  
  ✓ unauth player cannot remove own plays
  
  ✓ admin role can get ranking average
  
  ✓ player role cannot get ranking average
  
  ✓ unauth user cannot get ranking average
  
  ✓ admin role can get loser player
  
  ✓ player role cannot get loser player
  
  ✓ unauth user cannot get loser player
  
  ✓ admin role can get winner player
  
  ✓ player role cannot get winner player
  
  ✓ unauth user cannot get winner player
  
  Tests:  32 passed
  
  Time:   3.36s

