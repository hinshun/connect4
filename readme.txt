Team of 2:
Edgar Lee - 998711540 - g2lee
Livia Pasol - 999127813 - g2pasoll

Technologies:
In this application, we used Javascript, JQuery, PHP and CodeIgniter as our MVC platform. Twitter Bootstrap was for rapid front-end development, and custom CSS to tailor our site.

Validation:
Client and server side validation is performed on input forms like registration, logging in and password related tools. In addition, on POST of board states from the game, win conditions and board validity are checked server-sided. Board validity is whether or not the board is legal or not, i.e. it is possible to reach that board state from a previous, and that the board being POSTed to the server makes sense.

Program flow:
When the user enters the site, they are redirected to the login page. From there they can register, recover their password or login. Once logged in they access the game lobby, which contains online users (a button for challenging those that are available) and a leaderboard.
They can enter a game by challenging an opponent where a panel will appear and wait for their response (which can also be cancelled). The opponent will have an accept/decline panel popup on their end.
Once in game, the game continues until one user has won. If there is a tie, the game is reset and continues. When the game is over, they can click the site banner to return to the lobby, or simply refresh the page to be redirected.

Anti-cheating:
Since the javascript source is open to the public, and accessable via console or other means. The client can never be trusted for their game state. Hence, the server side always does validation on the board state on every POST so that an illegal change to the board state cannot be made. At most, the user can edit their client however much they want but the game state will not change in the database.

Additional features:
Password strength - A small javascript application to assess the strength of the user's password upon registration. Strength levels are increased through using 6 characters or more in their password, at least one upper and lower case letter and using an alphanumeric password.
Invitation cancellation - This feature allows users who have invited another player to cancel the invitation they sent. In case of users that will never respond, they can get out of the invitation.
Leaderboard - A table is constructed ordered by win/loss ratios of all the users on the site with one match or more.
Repopulate on refresh - On page reloads or re-visits to board/index to a match in progress, the game repopulates the game field and ensures the game can be continued.

Assumptions:
-When the user is going to quit the application, the user will use the 		log out dropdown from the button in the lobby. The server only ends a user session and sets their status as OFFLINE through that logout button.
-All games will be finished, otherwise the user can reset their status by logging off and logging back in. However, the game will be forever discontinued in the database.
