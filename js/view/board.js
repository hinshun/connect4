/******************
** PHP VARIABLES **
*******************/
var base_url = $('#base_url').val(); // Base url of server/application
var user = $('#user').val(); // The login name of the user in session
var userFirst = $('#userFirst').val(); // The first name of the user in session
var otherUser = $('#otherUser').val(); // The login name of the opponent
var otherUserFirst = $('#otherUserFirst').val(); // The first name of the opponent
var user1 = $('#user1').val(); // The login name of the user1_id of the match
var user2 = $('#user2').val(); // The login name of the user2_id of the match

/**************
** CONSTANTS **
***************/
var BOARD_WIDTH = 7; // Width of the game board
var BOARD_HEIGHT = 6; // Height of the game board
var DISC_OFFSET = 39; // The marginTop offset of the discs from the top of the board
var DISC_DIAMETER = 70; // The diameter of discs

// Indices on the board_state array
var EXTRA_STATE_VARS = 3; // The number of extra variables in the board state stored on DB
var USER1_TIME = BOARD_WIDTH * BOARD_HEIGHT; // The index for time remaining for user1
var USER2_TIME = BOARD_WIDTH * BOARD_HEIGHT + 1; // The index for time remaining for user2
var LAST_POSITION = 44; // The index for the last played move

/*********************
** GLOBAL VARIABLES **
**********************/
var Game;
// Locks the game state so that a new board cannot be retrieved through a GET
// request while a move is being made
var stateLock = false;
var displayedOnce = false; // Whether the player turn has been displayed once yet or not

/********************************************
******************************** GAME CLASS *
*********************************************/
function Game() {
	this.board = new Array(BOARD_WIDTH * BOARD_HEIGHT);
	// An array to keep track of the height of columns in the board
	this.columnHeights = new Array(BOARD_WIDTH);
	this.user1Time = 0;
	this.user2Time = 0;
	this.lastPosition = null;
	this.ended = false; // Whether the game has ended or not, stops GET requests
}

/*
 * Initializes the game by hiding GUI elements, adding system messages
 * to the chat and resetting board state
 */
Game.prototype.initialize = function() {
	// Hide GUI elements
	$('.board-arrow').css('visibility', 'hidden');
	$('.board-text').hide();
	// Update player colors
	$('#board-red-player').html(this.getUserFirst(1));
	$('#board-yellow-player').html(this.getUserFirst(2));
	
	// Initializes game variables
	for (var i = 0; i < this.board.length; i++)
		this.board[i] = 0;
	
	for (var i = 0; i < this.columnHeights.length; i++)
		this.columnHeights[i] = 0;
		
	this.user1Time = 0;
	this.user2Time = 0;
}

/*
 * Clears the board and column heights and clears the GUI discs from the DOM
 */
Game.prototype.clear = function() {
	for (var i = 0; i < this.board.length; i++)
		this.board[i] = 0;
	for (var i = 0; i < this.columnHeights.length; i++)
		this.columnHeights[i] = 0;
	$('#disc-wrapper').empty();
}

/*
 * Refreshes the discs on the board to be consistent with the board state given
 */
Game.prototype.refresh = function(board_state) {
	this.clear();
	for (var i = 0; i < board_state.length; i++) {
		this.board[i] = board_state[i];
		if (board_state[i] != 0) {
			this.addDisc(this.getColumn(i), board_state[i]);
		}
	}
}

/*
 * Populates the board with additional discs from board state but if there is
 * inconsistency, the board is refreshed completely
 */
Game.prototype.populate = function(board_state) {
	for (var i = 0; i < board_state.length; i++) {
		if ((this.board[i] != 0 && board_state[i] != 0 && this.board[i] != board_state[i]) ||
			(this.board[i] != 0 && board_state[i] == 0)) {
			addSystemMessage('Faulty board found. Refreshing the game board.');
			this.refresh(board_state);
			return;
		}
		
		if (this.board[i] == 0 && this.board[i] != board_state[i]) {
			this.addDisc(this.getColumn(i), board_state[i]);
			this.board[i] = board_state[i];
		}
	}
}

/*
 * Ends the game by disabling the board and displaying the winning message
 */
Game.prototype.end = function(status) {
	this.disableBoard();
	if (status == 2 && this.getUserIndex() == 1)
		this.displayWin();
	else if (status == 3 && this.getUserIndex() == 2)
		this.displayWin();
	else
		this.displayLose();
	this.ended = true;
}

/*
 * Allows the user to make a move
 */
Game.prototype.enableBoard = function() {
	$('.board-blocker').hide();
}

/*
 * Disallows the user to make a move
 */
Game.prototype.disableBoard = function() {
	$('.board-blocker').show();
}

/*
 * Checks who's turn it is and enables/disables the board accordingly
 */
Game.prototype.checkTurn = function() {
	if(this.isUserTurn()) {
		this.displayUserTurn();
		this.enableBoard();
	} else {
		this.displayOtherUserTurn();
		this.disableBoard();
	}
}

/*
 * Adds and animates a disc onto the board and updates the column
 * height array
 */
Game.prototype.addDisc = function(column, userIndex) {
	var color = userIndex == 1 ? 'red' : 'yellow';
	var row = this.columnHeights[column];
	this.columnHeights[column]++;
	
	// Appends the disc to the DOM
	$('#disc-wrapper').append('<img id="' + 'disccol-' + column + 'row-' + row + '" class="disc col-' + column + '" src="' + base_url + 'css/images/' + color + 'disc.png"/>');
	var marginTop = DISC_OFFSET + (5 - row) * DISC_DIAMETER;
	var disc = $('#disccol-' + column + 'row-' + row);
	disc.animate({'marginTop': marginTop}, 500);
	
	// Since the discs are now on top of the column selector divs, new
	// event handlers are given
	disc.mouseenter(function() {
		$('#arrow-' + column).css('visibility', 'visible');
	});
	disc.mouseleave(function () {
		$('#arrow-' + column).css('visibility', 'hidden');
	});
}

/*
 * Makes a move in column by user userIndex and sends a POST
 */
Game.prototype.makeMove = function(column, userIndex) {
	var position = this.getPosition(this.columnHeights[column], column);
	this.addDisc(column, userIndex);
	this.updateBoard(position, userIndex);
	this.postBoard(position, userIndex);
}

/*
 * Update the local board state and the last move made
 */
Game.prototype.updateBoard = function(position, userIndex) {
	this.board[position] = userIndex;
	this.lastPosition = position;
}

/*
 * POSTs the local board state to the server and progresses the
 * game according to the response
 */
Game.prototype.postBoard = function(position, userIndex) {
	var url = base_url + 'board/postBoard';
	var data = Game.serialize();
	$.post(url, data, function (data,textStatus,jqXHR) {
		data = JSON.parse(data);
		if (data.status == 'success') { // Success, progresses to next turn
			Game.nextPlayer();
		} else if (data.status == 'failure') { // Local board state is not possible
			addSystemMessage('Game state is invalid. Getting fresh copy from server.')
			Game.clear();
			Game.disableBoard(); // Disables the board and waits for GET request to update
		} else if (data.status == 'tie') { // If tie, then play again
			Game.clear();
			Game.disableBoard(); // Disables the board and waits for GET request to update
		} else // Game is over
			Game.end(data.match_status);
		stateLock = false;
	});
}

/*
 * Serializes the game into JSON
 */
Game.prototype.serialize = function() {
	if (!this.board)
		return { 'board_state' : null }

	var board_state = new Array(BOARD_WIDTH * BOARD_HEIGHT + EXTRA_STATE_VARS);
	for (var i = 0; i < board_state.length - EXTRA_STATE_VARS; i++) {
		board_state[i] = this.board[i];
	}
	
	board_state[USER1_TIME] = this.user1Time;
	board_state[USER2_TIME] = this.user2Time;
	board_state[LAST_POSITION] = this.lastPosition;
	
	return { 'board_state' : JSON.stringify(board_state) }
}

/*
 * Deserializes the retrieved board state and updates the local board state
 */
Game.prototype.deserialize = function(board_state) {
	var board_state_slice = board_state.slice(0, Game.board.length);
	if (this.isZeroState(board_state)) {
		Game.clear();
		Game.lastPosition = null;
	} else {
		Game.populate(board_state_slice);
		Game.lastPosition = board_state[LAST_POSITION];
	}
	
	Game.user1Time = board_state[USER1_TIME];
	Game.user2Time = board_state[USER2_TIME];
}

/*
 * Displays the next user's turn
 */
Game.prototype.nextPlayer = function() {
	if (this.getPlayerTurn() == this.getUserIndex()) {
		this.displayUserTurn();
	} else {
		this.displayOtherUserTurn();
	}
}

/*
 * Display that it is the user's turn
 */
Game.prototype.displayUserTurn = function() {
	$('.board-text').hide();
	$('#board-text-userTurn').fadeIn().delay(1000).fadeOut();
}

/*
 * Display that it is the opponent's turn
 */
Game.prototype.displayOtherUserTurn = function() {
	$('.board-text').hide();
	$('#board-text-otherUserTurn').fadeIn().delay(1000).fadeOut();
}

/*
 * Display that the user has won
 */
Game.prototype.displayWin = function() {
	$('.board-text').hide();
	$('#board-text-win').fadeIn().delay(1000).fadeOut();
}

/*
 * Display that the user has lost
 */
Game.prototype.displayLose = function() {
	$('.board-text').hide();
	$('#board-text-lose').fadeIn().delay(1000).fadeOut();
}

/*
 * Returns the number of turns that have been made on the board
 */
Game.prototype.getTurns = function() {
	var turns = 0;
	for (var i = 0; i < this.board.length; i++) {
		if (this.board[i] != 0)
			turns++;
	}
	return turns;
}

/*
 * Returns the index of the next player to play on the board
 */
Game.prototype.getPlayerTurn = function() {
	return this.getTurns() % 2 == 0 ? 1 : 2;
}

/*
 * Returns the index of the user
 */
Game.prototype.getUserIndex = function() {
	return user1 == user ? 1 : 2;
}

/*
 * Returns the first name of the user of userIndex
 */
Game.prototype.getUserFirst = function(userIndex) {
	if (userIndex == 1)
		return user1 == user ? userFirst : otherUserFirst;
	else
		return user2 == user ? userFirst : otherUserFirst;
}

/*
 * Returns the position from a given row and column
 */
Game.prototype.getPosition = function(row, column) {
	return parseInt(row * BOARD_WIDTH) + parseInt(column);
}

/*
 * Returns the first name of the winner derived from the match status
 */
Game.prototype.getWinnerFirst = function(status) {
	if (status == 2)
		return this.getUserFirst(1);
	else if (status == 3)
		return this.getUserFirst(2);
	return null;
}

/*
 * Returns the row from a given position
 */
Game.prototype.getRow = function(position) {
	return Math.floor(position / BOARD_WIDTH);
}

/*
 * Returns the column from a given position
 */
Game.prototype.getColumn = function(position) {
	return position % BOARD_WIDTH;
}

/*
 * Returns whether or not it is the user's turn
 */
Game.prototype.isUserTurn = function() {
	return this.getPlayerTurn() == this.getUserIndex();
}

/*
 * Returns whether a column is full or not
 */
Game.prototype.isFull = function(column) {
	return this.columnHeights[column] == BOARD_HEIGHT;
}

/*
 * Returns whether or not the local board state equals the given board state
 */
Game.prototype.isEqualTo = function(board_state) {
	var board_state_slice = board_state.slice(0, Game.board.length);
	return Game.board.equals(board_state_slice);
}

/*
 * Returns whether the board is an array of all zeroes or not
 */
Game.prototype.isZeroState = function(board_state) {
	for (var i = 0; i < board_state.length; i++) {
		if (board_state[i] != 0)
			return false;
	}
	return true;
}

/************************************************
******************************** EVENT HANDLERS *
*************************************************/
/*
 * Sets event handlers for board events
 */
function setGameEventHandlers() {
	$('body').everyTime(2000, function () {
		checkMessages(); // Checks for new messages
		
		if (!Game.ended) // Check for changes in the board state
			checkBoard(); // if the game has not ended
	});
		
	$('.board-column').on('click', function () {
		var column = $(this).attr('id').substring(4);
		
		if (!Game.isFull(column)) { // If the column is not full
			// Locks any board GET requests until the new move has been POSTed
			stateLock = true; 
			Game.disableBoard(); // Disables any further moves
			Game.makeMove(column, Game.getUserIndex());
		}
	});
	
	$('.board-column').mouseenter(function() {
		var column = $(this).attr('id').substring(4);
		$('#arrow-' + column).css('visibility', 'visible');
	});
	
	$('.board-column').mouseleave(function () {
		var column = $(this).attr('id').substring(4);
		$('#arrow-' + column).css('visibility', 'hidden');
	});
}

/*
 * Checks for new messages and updates DOM when there is
 */
function checkMessages() {
	var url = base_url + 'board/getMsg';
	$.getJSON(url, function (data,text,jqXHR) {
		if (data && data.status=='success') {
			var msg = data.message;
			if (msg && msg.length > 0) {
				addOtherMessage(msg);
			}
		}
	});
}

/*
 * Checks for changes in board state and updates local board 
 * state when there is
 */
function checkBoard() {
	var url = base_url + 'board/getBoard';
	$.getJSON(url, function (data,text,jqXHR) {
		if (data && data.status=='success') {
			var board_state = JSON.parse(data.board_state);
			// If board is not null and not locked by processing move
			if (board_state && !stateLock) { 
				if (!Game.isEqualTo(board_state)) { // Update only if board state is new
					var match_status = parseInt(data.match_status, 10);
					Game.deserialize(board_state);
					if (match_status == 1) // Game continues
						Game.checkTurn();
					else if (match_status == 0) // Game is a tie, reset
						Game.end(match_status);
					else // Someone has won
						Game.end(match_status);
				}	
			} else if (!board_state && !displayedOnce) { // Checks and displays turn once
				Game.checkTurn();
				displayedOnce = true;
			}
		}
	});
}

$(function () {
	Game = new Game();
	Game.initialize();
	setGameEventHandlers();
});